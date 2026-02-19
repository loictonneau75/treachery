<?php

namespace App\Security;

use App\Session\SessionTools;
use finfo;

require_once dirname(__DIR__) . "/session/tools.php";

final class CsrfTools
{
    private const TOKEN_KEY = "csrf_token";

    public static function generateToken(): void
    {
        if (SessionTools::getData(self::TOKEN_KEY) === null) {
            SessionTools::addData(self::TOKEN_KEY, bin2hex(random_bytes(32)));
        }
    }

    public static function validateToken(?string $token): void
    {
        if (
            empty($token) ||
            SessionTools::getData(self::TOKEN_KEY) === null ||
            !hash_equals(SessionTools::getData(self::TOKEN_KEY), $token)
        ) {
            http_response_code(403);
            exit("CSRF token invalide");
        }
    }

    public static function regenerateToken(): void
    {
        SessionTools::addData(self::TOKEN_KEY,bin2hex(random_bytes(32)));
    }
}

final class FormSecurity
{
    public static function protectForm(string $action, ?string $honeyPotValue = null, ?string $token = null): void{
        self::requirePost();
        CsrfTools::validateToken($token);
        self::rateLimit($action);
        self::throttle($action);
        self::honeyPot($honeyPotValue);
    }

    public static function validateFile(array $file, array $allowedTypes, int $maxSize): string{
        self::validateFileUpload($file);
        self::validateFileSize($file, $maxSize);
        $extension = self::validateFileExtension($file, $allowedTypes);
        self::validateFileType($file, $extension);
        return $extension;
    }

    private static function requirePost(): void{
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Méthode non autorisée');
        }
    }

    private static function honeyPot(?string $field): void{
        if (!empty($field)) {
            http_response_code(403);
            exit('Bot détecté');
        }
    }

    private static function rateLimit(string $action, int $limit = 5, int $windowSeconds = 60): void{
        $key = "rate_{$action}_" . $_SERVER['REMOTE_ADDR'];
        if (SessionTools::getData($key) === null) {
            SessionTools::addData($key, ['count' => 1, 'start' => time()]);
            return;
        }

        if (time() - $_SESSION[$key]['start'] > $windowSeconds) {
            SessionTools::addData($key, ['count' => 1,'start' => time()]);
            return;
        } 

        SessionTools::getData($key)['count']++;

        if (SessionTools::getData($key)['count'] > $limit) {
            http_response_code(429);
            exit("Trop de requêtes, réessayez plus tard.");
        }
        SessionTools::addData($key, SessionTools::getData($key));
    }

    private static function throttle(string $action, int $delaySeconds = 5): void{
        $key = "last_{$action}_" . $_SERVER['REMOTE_ADDR'];
        if (SessionTools::getData($key) !== null && (time() - SessionTools::getData($key)) < $delaySeconds) {
            http_response_code(429);
            exit("Trop rapide, réessayez plus tard.");
        }
        SessionTools::addData($key, time());
    }
    
    private static function validateFileUpload(array $file): void{
        if ($file['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            exit("Erreur lors du téléchargement du fichier");
        }
    }

    private static function validateFileSize(array $file, int $maxSize): void{
        if ($file['size'] > $maxSize) {
            http_response_code(413);
            exit("Le fichier est trop volumineux");
        }
    }
    private static function validateFileExtension(array $file, array $allowedExtensions): string{
        if (in_array('jpg', $allowedExtensions) && !in_array('jpeg', $allowedExtensions)) {
            $allowedExtensions[] = 'jpeg';
        }
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions)) {
            http_response_code(415);
            exit("extension de fichier non autorisé");
        }
        return $extension;
    }

    private static function validateFileType(array $file, string $extension): void{
        $extensionToMime = ['jpg'  => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png'  => 'image/png', 'gif'  => 'image/gif',];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!isset($extensionToMime[$extension]) || $mimeType !== $extensionToMime[$extension]) {
            http_response_code(415);
            exit("Type de fichier non autorisé");
        }
    }
}


<?php

namespace App\Security;

use App\Session\SessionTools;

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

    public static function requirePost(): void{
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Méthode non autorisée');
        }
    }

    public static function honeyPot(?string $field): void{
        if (!empty($field)) {
            http_response_code(403);
            exit('Bot détecté');
        }
    }

    public static function rateLimit(string $action, int $limit = 5, int $windowSeconds = 60): void{
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

    public static function throttle(string $action, int $delaySeconds = 5): void{
        $key = "last_{$action}_" . $_SERVER['REMOTE_ADDR'];
        if (SessionTools::getData($key) !== null && (time() - SessionTools::getData($key)) < $delaySeconds) {
            http_response_code(429);
            exit("Trop rapide, réessayez plus tard.");
        }
        SessionTools::addData($key, time());
    }
}


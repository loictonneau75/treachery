<?php
namespace App\Session;

use App\DB\DbTools;
use PDO;

class SessionTools {

    public static function sessionStart(){
        session_start();
    }

    public static function createSession(PDO $pdo, int $userId, bool $remember): void {
        session_regenerate_id(true);
        $_SESSION['id'] = $userId;
        if ($remember) {self::setRememberMe($pdo, $userId);}
    }

    public static function autoLogin(PDO $pdo): void {
        if (isset($_SESSION['id']) || empty($_COOKIE['remember_me'])) {
            return;
        }
        $tokenHash = hash('sha256', $_COOKIE['remember_me']);
        $row = DbTools::findRememberToken($pdo, $tokenHash);
        if (!$row || strtotime($row['expires_at']) < time()) {
            DbTools::deleteRememberTokenForHash($pdo, $tokenHash);
            self::clearRememberCookie();
            return;
        }
        $_SESSION['id'] = (int) $row['user_id'];
        self::setRememberMe($pdo, (int) $row['user_id']);
    }

    private static function setRememberMe(PDO $pdo, int $userId): void {
        $data = DbTools::createRememberToken($pdo, $userId);
        setcookie('remember_me', $data['token'], [
            'expires'  => $data['expiresAt']->getTimestamp(),
            'path'     => '/',
            'secure'   => true,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    public static function clearRememberCookie(): void {
        setcookie('remember_me', '', time() - 3600, '/', $_SERVER['HTTP_HOST']);
    }

    public static function getData($key){
        return $_SESSION[$key] ?? null;
    }

    public static function addData($key, $value){
        $_SESSION[$key] = $value;
    }

    public static function deleteSession(){
        $_SESSION = [];
        session_destroy();
    }
}

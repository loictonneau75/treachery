<?php
namespace App\Session;

use App\DB\DbTools;

class SessionTools {

    public static function sessionStart(): void {
        session_name('TREACHERY_SESSION');
        session_start();
    }

    public static function createSession(DbTools $db, int $userId, bool $remember): void {
        session_regenerate_id(true);
        $_SESSION['id'] = $userId;
        if ($remember) {self::setRememberMe($db, $userId);}
    }

    public static function autoLogin(DbTools $db): void {
        if (isset($_SESSION['id']) || empty($_COOKIE['remember_me'])) {
            return;
        }
        $tokenHash = hash('sha256', $_COOKIE['remember_me']);
        $row = $db -> findRememberToken($tokenHash);
        if (!$row || strtotime($row['expires_at']) < time()) {
            $db -> deleteRememberTokens(['token_hash' => $tokenHash]);
            self::clearRememberCookie();
            return;
        }
        $_SESSION['id'] = (int) $row['user_id'];
        self::setRememberMe($db, (int) $row['user_id']);
    }

    private static function setRememberMe(DbTools $db, int $userId): void {
        $data = $db -> createRememberToken($userId);
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

    //todo donner un type au paramètre $key et $value
    public static function getData($key): mixed{
        return $_SESSION[$key] ?? null;
    }

    public static function addData($key, $value): void{
        $_SESSION[$key] = $value;
    }

    public static function deleteSession(): void{
        $_SESSION = [];
        session_destroy();
    }
}

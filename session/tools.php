<?php
namespace App\Session;

use App\DB\DbTools;

require_once dirname(__DIR__,2) . "/db/tools.php";

class SessionTools {
    private DbTools $db;

    public function __construct(DbTools $db){
        $this -> db = $db;
        session_name('TREACHERY_SESSION');
        session_start();
    }

    public function createSession(int $userId, bool $remember): void {
        session_regenerate_id(true);
        $_SESSION['id'] = $userId;
        if ($remember) {$this -> setRememberMe($userId);}
    }

    public function autoLogin(): void {
        if (isset($_SESSION['id']) || empty($_COOKIE['remember_me'])) {
            return;
        }
        $tokenHash = hash('sha256', $_COOKIE['remember_me']);
        $row = $this -> db -> findRememberToken($tokenHash);
        if (!$row || strtotime($row['expires_at']) < time()) {
            $this -> db -> deleteRememberTokensByTokenHash($tokenHash);
            self::clearRememberCookie();
            return;
        }
        $_SESSION['id'] = (int) $row['user_id'];
        $this -> setRememberMe((int) $row['user_id']);
    }

    private function setRememberMe(int $userId): void {
        $data = $this -> db -> createRememberToken($userId);
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

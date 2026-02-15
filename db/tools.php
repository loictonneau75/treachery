<?php
namespace App\DB;

use PDO;
use DateTime;

class DbTools{
    public static function userEmailOrPseudoExist(PDO $pdo, string $email, string $pseudo): bool{
        $stmt = $pdo -> prepare("SELECT 1 FROM users WHERE email = ? OR pseudo = ? LIMIT 1");
        $stmt -> execute([$email, $pseudo]);
        return (bool)$stmt->fetch();
    }

    public static function createUser(PDO $pdo, string $pseudo, string $email, string $password): int{
        $stmt = $pdo -> prepare("INSERT INTO users (pseudo, email, password) VALUES (?, ?, ?)");
        $stmt -> execute([$pseudo, $email, password_hash($password, PASSWORD_DEFAULT)]);
        return (int)$pdo -> lastInsertId();
    }

    public static function createRememberToken(PDO $pdo, int $userId): array {
        self::deleteExpiredRememberTokens($pdo);
        self::deleteRememberTokensForUser($pdo, $userId);
        $token     = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = new DateTime('+30 days');
        $stmt = $pdo->prepare('INSERT INTO remember_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)');
        $stmt->execute([$userId,$tokenHash,$expiresAt->format('Y-m-d H:i:s')]);
        return ['token' => $token, 'expiresAt' => $expiresAt];
    }

    public static function findRememberToken(PDO $pdo, string $tokenHash): ?array {
        $stmt = $pdo->prepare('SELECT user_id, expires_at FROM remember_tokens WHERE token_hash = ? LIMIT 1');
        $stmt->execute([$tokenHash]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function deleteRememberTokenForHash(PDO $pdo, string $tokenHash): void {
        $stmt = $pdo->prepare('DELETE FROM remember_tokens WHERE token_hash = ?');
        $stmt->execute([$tokenHash]);
    }

    public static function deleteRememberTokensForUser(PDO $pdo, int $userId): void {
        $stmt = $pdo->prepare('DELETE FROM remember_tokens WHERE user_id = ?');
        $stmt->execute([$userId]);
    }

    public static function deleteExpiredRememberTokens(PDO $pdo): void {
        $stmt = $pdo->prepare('DELETE FROM remember_tokens WHERE expires_at < NOW()');
        $stmt->execute();
    }

    public static function verifyUser(PDO $pdo, string $email, string $password): false|int {
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return false;
        }
        if (password_verify($password, $user['password'])) {
            return (int)$user['id'];
        }
        return false;
    }

    public static function getPseudoById(PDO $pdo, int $id): string{
        $stmt =$pdo->prepare("SELECT pseudo FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['pseudo'];
    }

    public static function deleteUserById(PDO $pdo, int $userId): void{
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
    }

    public static function getRolesData(PDO $pdo){
        $stmt = $pdo->query("SELECT * From `roles`");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getRarityData(PDO $pdo){
        $stmt = $pdo->query("SELECT * From `rarities`");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}





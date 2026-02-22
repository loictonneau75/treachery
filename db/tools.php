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
        self::deleteRememberTokens($pdo, ['expires_at' => 'expired']);
        self::deleteRememberTokens($pdo, ['user_id' => $userId]);
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

    public static function deleteRememberTokens(PDO $pdo, array $conditions = []): void{
        $sql = "DELETE FROM remember_tokens";
        $params = [];
        $clauses = [];
        $allowedColumns = ['token_hash', 'user_id', 'expires_at'];
        foreach ($conditions as $column => $value) {
            if (!in_array($column, $allowedColumns)) {throw new InvalidArgumentException("Colonne non autorisée");}
            if ($column === 'expires_at' && $value === 'expired') {$clauses[] = "expires_at < NOW()";} 
            else {
                $clauses[] = "$column = ?";
                $params[] = $value;
            }
        }
        if (!empty($clauses)) {$sql .= " WHERE " . implode(" AND ", $clauses);}
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }


    public static function verifyUser(PDO $pdo, string $email, string $password): false|int {
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }
        return (int)$user['id'];
    }

    public static function deleteUserById(PDO $pdo, int $userId): void{
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
    }

    public static function getFieldById(PDO $pdo, string $table, string $field, int $id): ?string {
        $stmt = $pdo->prepare("SELECT $field FROM $table WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row[$field];
    }

    public static function getAllFrom(PDO $pdo, string $table): array{
        $allowedTables = ['roles', 'rarities'];
        if (!in_array($table, $allowedTables)) {throw new InvalidArgumentException("Table non autorisée");}
        $stmt = $pdo->query("SELECT * FROM $table");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function recordExists(PDO $pdo, string $table, int $id): bool{
        $allowedTables = ['roles', 'rarities'];
        if (!in_array($table, $allowedTables)) {throw new InvalidArgumentException("Table non autorisée");}
        $stmt = $pdo->prepare("SELECT 1 FROM $table WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return (bool) $stmt->fetchColumn();
    }

    public static function createCard(PDO $pdo, string $imgPath, int $rarityId, int $roleId, int $userId): void{
        $stmt = $pdo -> prepare("INSERT INTO cards (path, rarity_id, role_id, user_id) VALUES (?, ?, ?, ?)");
        $stmt -> execute([$imgPath, $rarityId, $roleId, $userId]);
    }

    public static function getCardsBy(PDO $pdo, array $conditions = [], string $orderBy = ""): array{
        $sql = "SELECT * FROM cards";
        $params = [];
        if (!empty($conditions)) {
            $clauses = [];
            foreach ($conditions as $column => $value) {
                $clauses[] = "$column = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(" AND ", $clauses);
        }
        if ($orderBy !== "") $sql .= " ORDER BY $orderBy ASC";
        error_log($sql);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}





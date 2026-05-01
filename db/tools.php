<?php
namespace App\DB;

use PDO;
use DateTime;
use Exception;

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

    public static function deleteById(PDO $pdo, string $table, int $id): void {
        $stmt = $pdo->prepare("DELETE FROM $table WHERE id = ?");
        $stmt->execute([$id]);
    }

    public static function getFieldById(PDO $pdo, string $table, string $field, int $id): ?string {
        $stmt = $pdo->prepare("SELECT $field FROM $table WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row[$field];
    }

    public static function getCardRoles(PDO $pdo, array $cardsSelected): array {
        $cardsSelected = array_map('intval', $cardsSelected);
        $placeholders = implode(',', array_fill(0, count($cardsSelected), '?'));
        $stmt = $pdo->prepare("SELECT role_id FROM cards WHERE id IN ($placeholders)");
        $stmt->execute($cardsSelected);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function getById(PDO $pdo, string $table, int $id): ?array {
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ;
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
        $stmt = $pdo -> prepare("INSERT INTO cards (path, rarity_id, role_id, added_by) VALUES (?, ?, ?, ?)");
        $stmt -> execute([$imgPath, $rarityId, $roleId, $userId]);
    }

    public static function createRoom(PDO $pdo, string $code, int $maxPlayer):int{
        $stmt = $pdo -> prepare("INSERT INTO rooms (code, max_player) VALUES (?, ?)");
        $stmt -> execute([$code, $maxPlayer]);
        return (int)$pdo -> lastInsertId();
    }

    public static function addCardToRoom(PDO $pdo, int $roomId, int $cardId): void{
        $stmt = $pdo -> prepare("INSERT INTO room_card (room_id, card_id) VALUES (?, ?)");
        $stmt -> execute([$roomId, $cardId]);
    }


    public static function addPlayerToRoom(PDO $pdo, int $roomId, int $userId): void {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("UPDATE rooms SET current_players = current_players + 1 WHERE id = :roomId AND current_players < max_player");
            $stmt->execute(['roomId' => $roomId]);
            if ($stmt->rowCount() === 0) {
                $pdo->rollBack();
                throw new Exception("Salon complet");
            }
            $stmt = $pdo->prepare("INSERT INTO room_player (room_id, user_id) VALUES (:roomId, :userId)");
            $stmt->execute([
                'roomId' => $roomId,
                'userId' => $userId
            ]);
            $pdo->commit();
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {$pdo->rollBack();}
            throw $e;
        }
    }


    public static function roomExist(PDO $pdo, string $code): bool{
        $stmt = $pdo -> prepare("SELECT 1 FROM rooms WHERE code = ? LIMIT 1");
        $stmt -> execute([$code]);
        return (bool)$stmt->fetch();
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
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getFieldByCode(PDO $pdo, string $table, string $field, string $code): ?string {
        $stmt = $pdo->prepare("SELECT $field FROM $table WHERE code = ?");
        $stmt->execute([$code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row[$field] ?? null;
    }

    public static function isUserInRoom(PDO $pdo, int $roomId, int $userId): bool {
        $stmt = $pdo->prepare("SELECT 1 FROM room_player WHERE room_id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$roomId, $userId]);
        return (bool) $stmt->fetch();
    }

}





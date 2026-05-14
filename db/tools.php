<?php
namespace App\DB;

use PDO;
use DateTime;
use Exception;
use InvalidArgumentException;

class DbTools{
    private PDO $pdo;

    public function __construct(PDO $pdo){
        $this -> pdo = $pdo;
    }

    private function delete(string $table, array $conditions, string $operator = "AND"): void {
        $allowedColumns = [
            'remember_tokens' => ['token_hash', 'user_id', 'expires_at'], 
            'users' => ['id'], 
            'cards' => ['id']
        ];
        if (!array_key_exists($table, $allowedColumns)) throw new InvalidArgumentException("Table non autorisée");
        $sql = "DELETE FROM $table";
        $params = []; 
        $clauses = []; 
        foreach ($conditions as $column => $value) {
            if (!in_array($column, $allowedColumns[$table])) throw new InvalidArgumentException("Colonne non autorisée");
            if ($column === 'expires_at' && $value === 'expired') $clauses[] = "expires_at < NOW()";
            else {
                $clauses[] = "$column = ?"; 
                $params[] = $value; 
            } 
        } 
        if (!empty($clauses)) $sql .= " WHERE " . implode(" $operator ", $clauses);
        $stmt = $this -> pdo -> prepare($sql); 
        $stmt ->execute($params); 
    }

    public function deleteExpiredRememberTokens(): void{
        $this -> delete('remember_tokens', ['expires_at' => 'expired']);
    }

    public function deleteRememberTokensByUserId(int $userId): void{
        $this -> delete('remember_tokens', ['user_id' => $userId]);
    }

    public function deleteRememberTokensByTokenHash(string $tokenHash): void{
        $this -> delete('remember_tokens', ['token_hash' => $tokenHash]);
    }

    public function deleteUserById(int $userId): void {
        $this -> delete('users', ['id' => $userId]);
    }

    public function deleteCardById(int $cardId): void {
        $this -> delete('cards', ['id' => $cardId]);
    }




















    public function userEmailExist(string $email, string $pseudo): bool{
        $stmt = $this -> pdo -> prepare("SELECT 1 FROM users WHERE email = ? LIMIT 1");
        $stmt -> execute([$email]);
        return (bool)$stmt->fetch();
    }

    public function createUser(string $pseudo, string $email, string $password): int{
        $stmt = $this -> pdo -> prepare("INSERT INTO users (pseudo, email, password) VALUES (?, ?, ?)");
        $stmt -> execute([$pseudo, $email, password_hash($password, PASSWORD_DEFAULT)]);
        return (int)$this -> pdo -> lastInsertId();
    }

    public function createRememberToken(int $userId): array {
        $this -> deleteExpiredRememberTokens();
        $this -> deleteRememberTokensByUserId($userId);
        $token     = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = new DateTime('+30 days');
        $stmt = $this -> pdo -> prepare('INSERT INTO remember_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)');
        $stmt -> execute([$userId, $tokenHash, $expiresAt -> format('Y-m-d H:i:s')]);
        return ['token' => $token, 'expiresAt' => $expiresAt];
    }

    public function findRememberToken(string $tokenHash): ?array {
        $stmt = $this -> pdo -> prepare('SELECT user_id, expires_at FROM remember_tokens WHERE token_hash = ? LIMIT 1');
        $stmt -> execute([$tokenHash]);
        return $stmt -> fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function verifyUser(string $email, string $password): false|int {
        $stmt = $this -> pdo -> prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt -> execute([$email]);
        $user = $stmt -> fetch(PDO::FETCH_ASSOC);
        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }
        return (int)$user['id'];
    }

    public function getFieldById(string $table, string $field, int $id): ?string {
        $stmt = $this -> pdo -> prepare("SELECT $field FROM $table WHERE id = ?");
        $stmt -> execute([$id]);
        $row = $stmt -> fetch(PDO::FETCH_ASSOC);
        return $row[$field];
    }

    public function getCardRoles(array $cardsSelected): array {
        $cardsSelected = array_map('intval', $cardsSelected);
        $placeholders = implode(',', array_fill(0, count($cardsSelected), '?'));
        $stmt = $this -> pdo -> prepare("SELECT role_id FROM cards WHERE id IN ($placeholders)");
        $stmt -> execute($cardsSelected);
        return $stmt -> fetchAll(PDO::FETCH_COLUMN);
    }

    public function getById(string $table, int $id): ?array {
        $stmt = $this -> pdo -> prepare("SELECT * FROM $table WHERE id = ?");
        $stmt -> execute([$id]);
        $row = $stmt -> fetch(PDO::FETCH_ASSOC);
        return $row ;
    }


    public function getAllFrom(string $table): array{
        $allowedTables = ['roles', 'rarities'];
        if (!in_array($table, $allowedTables)) {throw new InvalidArgumentException("Table non autorisée");}
        $stmt = $this -> pdo -> query("SELECT * FROM $table");
        return $stmt -> fetchAll(PDO::FETCH_ASSOC);
    }

    public function recordExists(string $table, int $id): bool{
        $allowedTables = ['roles', 'rarities'];
        if (!in_array($table, $allowedTables)) {throw new InvalidArgumentException("Table non autorisée");}
        $stmt = $this -> pdo -> prepare("SELECT 1 FROM $table WHERE id = ? LIMIT 1");
        $stmt -> execute([$id]);
        return (bool) $stmt -> fetchColumn();
    }

    public function createCard(string $imgPath, int $rarityId, int $roleId, int $userId): void{
        $stmt = $this -> pdo -> prepare("INSERT INTO cards (path, rarity_id, role_id, added_by) VALUES (?, ?, ?, ?)");
        $stmt -> execute([$imgPath, $rarityId, $roleId, $userId]);
    }

    public function createRoom(string $code, int $maxPlayer):int{
        $stmt = $this -> pdo -> prepare("INSERT INTO rooms (code, max_player) VALUES (?, ?)");
        $stmt -> execute([$code, $maxPlayer]);
        return (int)$this -> pdo -> lastInsertId();
    }

    public function addCardToRoom(int $roomId, int $cardId): void{
        $stmt = $this -> pdo -> prepare("INSERT INTO room_card (room_id, card_id) VALUES (?, ?)");
        $stmt -> execute([$roomId, $cardId]);
    }


    public function addPlayerToRoom(int $roomId, int $userId): void {
        try {
            $this -> pdo -> beginTransaction();
            $stmt = $this -> pdo -> prepare("UPDATE rooms SET current_players = current_players + 1 WHERE id = :roomId AND current_players < max_player");
            $stmt -> execute(['roomId' => $roomId]);
            if ($stmt -> rowCount() === 0) {
                $this -> pdo -> rollBack();
                throw new Exception("Salon complet");
            }
            $stmt = $this -> pdo -> prepare("INSERT INTO room_player (room_id, user_id) VALUES (:roomId, :userId)");
            $stmt->execute([
                'roomId' => $roomId,
                'userId' => $userId
            ]);
            $this -> pdo -> commit();
        } catch (Exception $e) {
            if ($this -> pdo -> inTransaction()) {$this -> pdo -> rollBack();}
            throw $e;
        }
    }


    public function roomExist(string $code): bool{
        $stmt = $this -> pdo -> prepare("SELECT 1 FROM rooms WHERE code = ? LIMIT 1");
        $stmt -> execute([$code]);
        return (bool)$stmt->fetch();
    }

    public function getCardsBy(array $conditions = [], string $orderBy = ""): array{
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
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute($params);
        return $stmt -> fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFieldByCode(string $table, string $field, string $code): ?string {
        $stmt = $this -> pdo -> prepare("SELECT $field FROM $table WHERE code = ?");
        $stmt -> execute([$code]);
        $row = $stmt -> fetch(PDO::FETCH_ASSOC);
        return $row[$field] ?? null;
    }

    public function isUserInRoom(int $roomId, int $userId): bool {
        $stmt = $this -> pdo -> prepare("SELECT 1 FROM room_player WHERE room_id = ? AND user_id = ? LIMIT 1");
        $stmt -> execute([$roomId, $userId]);
        return (bool)$stmt -> fetch();
    }

}





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

        if (!empty($clauses)) $sql .= " WHERE " . implode("$operator", $clauses);

        $stmt = $this -> pdo -> prepare($sql); 
        $stmt ->execute($params); 
    }

    public function deleteExpiredRememberTokens(): void{
        $this -> delete(table: 'remember_tokens', conditions: ['expires_at' => 'expired']);
    }

    public function deleteRememberTokensByUserId(int $userId): void{
        $this -> delete(table: 'remember_tokens', conditions: ['user_id' => $userId]);
    }

    public function deleteRememberTokensByTokenHash(string $tokenHash): void{
        $this -> delete(table: 'remember_tokens', conditions: ['token_hash' => $tokenHash]);
    }

    public function deleteUserById(int $userId): void {
        $this -> delete(table: 'users', conditions: ['id' => $userId]);
    }

    public function deleteCardById(int $cardId): void {
        $this -> delete(table: 'cards', conditions: ['id' => $cardId]);
    }

    private function exists(string $table, array $conditions, string $operator = "AND"): bool {
        $allowed = [
            'users' => ['email', 'pseudo'],
            'rooms' => ['code'],
            'roles' => ['id'],
            'rarities' => ['id'],
            'room_player' => ['room_id', 'user_id']
        ];

        if (!array_key_exists($table, $allowed)) throw new InvalidArgumentException("Table non autorisée");

        $sql = "SELECT 1 FROM $table"; 
        $params = [];
        $clauses = [];

        foreach ($conditions as $column => $value) {
            if (!in_array($column, $allowed[$table])) throw new InvalidArgumentException("Colonne non autorisée");
            $clauses[] = "$column = ?";
            $params[] = $value; 
        }

        if (!empty($clauses)) $sql .= " WHERE " . implode("$operator", $clauses);

        $sql .= " LIMIT 1";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute($params);

        return (bool) $stmt->fetchColumn(); 
    }

    public function existsUserEmail(string $email): bool{
        return $this -> exists(table: 'users', conditions: ['email' => $email]);
    }

    public function existsRole(int $roleId): bool{
        return $this -> exists(table: 'roles', conditions: ['id' => $roleId]);
    }

    public function existsRarity(int $rarityId): bool{
        return $this -> exists(table: 'rarities', conditions: ['id' => $rarityId]);
    }

    public function existsRoom(string $code): bool{
        return $this -> exists(table: 'rooms', conditions: ['code' => $code]);
    }

    public function existsUserInRoom(int $userId, int $roomId): bool{
        return $this -> exists(table: 'room_player', conditions: ['user_id' => $userId, 'room_id' => $roomId]);
    }
    
    /**
    * @param string $table Nom de la table cible.
    * @param array<array<string,mixed>> $data Liste des lignes à insérer.
    */
    private function insert(string $table, array $data): int {
        $allowed = [
            'users' => ['email', 'pseudo', 'password'],
            'cards' => ['path', 'rarity_id', 'role_id', 'added_by'],
            'rooms' => ['code', 'max_player'],
            'room_card' => ['room_id', 'card_id'],
            'room_player' => ['room_id', 'user_id'],
            'remember_tokens' => ['user_id', 'token_hash', 'expires_at'] 
        ]; 
        if (!array_key_exists($table, $allowed)) throw new InvalidArgumentException("Table non autorisée");
        if (empty($data)) throw new InvalidArgumentException("Aucune donnée à insérer");
        $placeholdersGroup = [];
        $values = [];
        foreach ($data as $rows) {
            if (array_keys($rows) !== array_keys($data[0])) throw new InvalidArgumentException("Les lignes doivent avoir les mêmes clés");
            foreach (array_keys($rows) as $column) {
                if (!in_array($column, $allowed[$table])) throw new InvalidArgumentException("Colonne non autorisée"); 
            }
            $placeholdersGroup[] = '(' . implode(',', array_fill(0, count($rows), '?')) . ')';
            foreach ($rows as $row) {
                $values[] = $row;
            }
        }
        $sql = "INSERT INTO $table (" . implode(',', array_keys($data[0])) . ") VALUES " . implode(',', $placeholdersGroup);
        $stmt = $this -> pdo -> prepare($sql);
        $stmt->execute($values);

        return (int) $this -> pdo -> lastInsertId();
    }

    public function insertUser(string $pseudo, string $email, string $password): int{
        return $this -> insert(
            table: 'users',
            data: [[
                'pseudo' => $pseudo, 
                'email' => $email, 
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ]]
        );
    }

    public function insertRememberToken(int $userId): array {
        $this -> deleteExpiredRememberTokens();
        $this -> deleteRememberTokensByUserId($userId);
        $token = bin2hex(random_bytes(32));
        $expiresAt = new DateTime('+30 days');
        $this -> insert(
            table: 'remember_tokens',
            data: [[
                'user_id' => $userId,
                'token_hash' => hash('sha256', $token),
                'expires_at' => $expiresAt -> format('Y-m-d H:i:s')
            ]]
        );
        return ['token' => $token, 'expiresAt' => $expiresAt];
    }

    public function insertCard(string $imgPath, int $rarityId, int $roleId, int $userId): void{
        $this -> insert(
            table: 'cards',
            data: [[
                'path' => $imgPath,
                'rarity_id' => $rarityId,
                'role_id' => $roleId,
                'added_by' => $userId
            ]]
        );
    }

    public function insertRoom(string $code, int $maxPlayer): int{
        return $this -> insert(
            table: 'rooms',
            data: [[
                'code' => $code,
                'max_player' => $maxPlayer
            ]]
        );
    }

    public function insertCardsToRoom(int $roomId, array $cardsIds): void{
        $this-> insert(
            table: 'room_card',
            data: array_map(fn($cardId) => [
                'room_id' => $roomId,
                'card_id' => $cardId
            ], $cardsIds)
        );
    }

    public function insertPlayerToRoom(int $roomId, int $userId): void {
        try {
            $this -> pdo -> beginTransaction();
            $stmt = $this -> pdo -> prepare("UPDATE rooms SET current_players = current_players + 1 WHERE id = :roomId AND current_players < max_player");
            $stmt -> execute(['roomId' => $roomId]);
            if ($stmt -> rowCount() === 0) {
                $this -> pdo -> rollBack();
                throw new Exception("Salon complet");
            }
            $this -> insert(
                table: 'room_player',
                data: [[
                    'room_id' => $roomId,
                    'user_id' => $userId
                ]]
            ); 
            $this -> pdo -> commit();
        } catch (Exception $e) {
            if ($this -> pdo -> inTransaction()) $this -> pdo -> rollBack();
            throw $e;
        }
    }

    private function query(string $table, array $columns = ['*'], array $conditions = [], array $in = [], ?string $orderBy = null, ?int $limit = null, string $fetchMode = 'all'): array|null { 
        $allowed = [
            "rarities" => [],
            "cards" => ['role_id', 'rarity_id', 'id'],
            "users" => ['id', 'email', 'pseudo', 'password', 'admin'],
            "roles" => ['id', 'name'],
            "rooms" => ['id', 'code'],
            "rememeber_tokens" => ['user_id', 'token_hash', 'expires_at'] 
        ]; 
        if (!array_key_exists($table, $allowed)) throw new InvalidArgumentException("Table non autorisée");

        foreach ($columns as $col) {
            if ($col !== '*' && !in_array($col, $allowed[$table], true)) throw new InvalidArgumentException("Colonne SELECT non autorisée"); 
        } 

        $sql = "SELECT " . implode(', ', $columns) . " FROM $table"; $params = [];

        if (!empty($conditions)) {
            $clauses = []; 
            foreach ($conditions as $column => $value) { 
                if (!in_array($column, $allowed[$table])) throw new InvalidArgumentException("Colonne non autorisée"); 
                $clauses[] = "$column = ?"; 
                $params[] = $value; 
            }
            $sql .= " WHERE " . implode(" AND ", $clauses); 
        }

        if (!empty($in)) {
            $clauses = [];
            foreach ($in as $col => $values) {
                if (!in_array($col, $allowed[$table], true)) throw new InvalidArgumentException("Colonne IN non autorisée");
                $placeholders = implode(',', array_fill(0, count($values), '?'));
                $clauses[] = "$col IN ($placeholders)"; $params = array_merge($params, $values); 
            }
            $sql .= empty($conditions) ? " WHERE " : " AND ";
            $sql .= implode(" AND ", $clauses);
        }

        if ($orderBy !== null) { 
            if (!in_array($orderBy, $allowed[$table])) throw new InvalidArgumentException("ORDER BY non autorisé");
            $sql .= " ORDER BY $orderBy ASC";
        }

        if ($limit !== null) $sql .= " LIMIT $limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        if ($fetchMode === 'one') return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    public function findRememberToken(string $tokenHash): ?array {
        return $this -> query(
            table: 'remember_tokens',
            columns: ['user_id', 'expires_at'],
            conditions: ['token_hash' => $tokenHash],
            limit: 1,
            fetchMode: 'one'
        );
    }

    public function verifyUser(string $email, string $password): false|int {
        $user = $this -> query(
            table: 'users',
            columns: ['id', 'password'],
            conditions: ['email' => $email],
            fetchMode: 'one'
        );
        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }
        return (int)$user['id'];
    }

    public function isUserAdmin(int $userId): bool {
        $user = $this -> query(
            table: 'users',
            columns: ['admin'],
            conditions: ['id' => $userId],
            fetchMode: 'one'
        );
        return (bool)$user['admin'];
    }

    public function getUserPseudo(int $userId): string {
        $user = $this -> query(
            table: 'users',
            columns: ['pseudo'],
            conditions: ['id' => $userId],
            fetchMode: 'one'
        );
        return htmlspecialchars($user['pseudo'], ENT_QUOTES, 'UTF-8');
    }

    public function getRoleName(int $roleId): string {
        $role = $this -> query(
            table: 'roles',
            columns: ['name'],
            conditions: ['id' => $roleId],
            fetchMode: 'one'
        );
        return $role['name'];
    }

    public function getCardRoles(array $cardsSelected): array {
        $role =  $this -> query(
            table: 'cards',
            columns: ['role_id'],
            in: ['id' => array_map('intval', $cardsSelected)]
        );
        return array_column($role, 'role_id');
    }


    public function getCardById(int $id): array {
        return $this -> query(
            table: 'cards',
            conditions: ['id' => $id],
            limit: 1,
            fetchMode: 'one'
        );
    }

    public function getRoles(){
        return $this -> query(
            table: 'roles'
        );
    }

    public function getRarities(){
        return $this -> query(
            table: 'rarities'
        );
    }

    public function getRoomByCode(string $code): ?int {
        return $this -> query(
            table: 'rooms',
            conditions: ['code' => $code],
            limit: 1,
            fetchMode: 'one'
        );
    }

    public function getCardByRoleId(int $roleId): array {
        return $this -> query(
            table: 'cards',
            conditions: ['role_id' => $roleId],
            orderBy: 'rarity_id'
        );
    }

    public function getCardByRarityId(int $rarityId): array {
        return $this -> query(
            table: 'cards',
            conditions: ['rarity_id' => $rarityId],
            orderBy: 'role_id'
        );
    }

}





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

    private function _deleteExpiredTokensAndUserTokens (int $userId): void{
        $sql = "DELETE FROM remember_tokens WHERE expires_at < NOW() OR user_id = ?";
        $stmt = $this -> pdo -> prepare($sql); 
        $stmt -> execute([$userId]); 
    }

    public function deleteRememberTokensByTokenHash(string $tokenHash): void{
        $sql = "DELETE FROM remember_tokens WHERE token_hash = ?";
        $stmt = $this -> pdo -> prepare($sql); 
        $stmt -> execute([$tokenHash]); 
    }

    public function deleteUserById(int $userId): void {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this -> pdo -> prepare($sql); 
        $stmt -> execute([$userId]); 
    }

    public function deleteCardById(int $cardId): void {
        $sql = "DELETE FROM cards WHERE id = ?";
        $stmt = $this -> pdo -> prepare($sql); 
        $stmt -> execute([$cardId]); 
    }

    private function _deleteUserFromRoom(int $userId): void{
        $sql = "DELETE FROM room_player WHERE user_id = ?";
        $stmt = $this -> pdo -> prepare($sql); 
        $stmt -> execute([$userId]);
    }

    private function _decrementRoomPlayerCount(int $roomId){
        $sql = "UPDATE rooms SET current_players = current_players - 1 WHERE id = ? AND current_players > 0";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute([$roomId]);
    }

    public function deleteUserFromRoom(int $userId, int $roomId): void{
        $this -> _deleteUserFromRoom($userId);
        $this -> _decrementRoomPlayerCount($roomId);
    }

    public function existsUserEmail(string $email): bool{
        $sql = "SELECT 1 FROM users WHERE email = ? LIMIT 1";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute([$email]);
        return (bool) $stmt->fetchColumn();
    }

    public function existsRole(int $roleId): bool{
        $sql = "SELECT 1 FROM roles WHERE id = ? LIMIT 1";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute([$roleId]);
        return (bool) $stmt->fetchColumn();
    }

    public function existsRarity(int $rarityId): bool{
        $sql = "SELECT 1 FROM rarities WHERE id = ? LIMIT 1";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute([$rarityId]);
        return (bool) $stmt->fetchColumn();
    }

    public function existsRoom(string $code): bool{
        $sql = "SELECT 1 FROM rooms WHERE code = ? LIMIT 1";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute([$code]);
        return (bool) $stmt->fetchColumn();
    }

    public function existsUserInRoom(int $userId, int $roomId){
        $sql = "SELECT 1 FROM room_player WHERE user_id = ? AND room_id = ? LIMIT 1";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute([$userId, $roomId]);
        return (bool) $stmt->fetchColumn();
    }
    
    /**
    * @param string $table Nom de la table cible.
    * @param array<array<string,mixed>> $data Liste des lignes à insérer.
    */
    private function insert(string $table, array $data) {
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
        echo $sql;

        // $stmt = $this -> pdo -> prepare($sql);
        // $stmt->execute($values);

        // return (int) $this -> pdo -> lastInsertId();
    }

    public function insertUser(string $pseudo, string $email, string $password): int{
        $sql = "INSERT INTO users (pseudo, email, password) VALUES (?, ?, ?)";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute([$pseudo, $email, $password]);
        return (int) $this -> pdo -> lastInsertId();
    }

    public function insertRememberToken(int $userId): array {
        $this -> _deleteExpiredTokensAndUserTokens($userId);
        $token = bin2hex(random_bytes(32));
        $expiresAt = new DateTime('+30 days');
        $sql = "INSERT INTO remember_tokens (user_id,token_hash,expires_at) VALUES (?,?,?)";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute([$userId, hash('sha256', $token), $$expiresAt -> format('Y-m-d H:i:s')]);
        return ['token' => $token, 'expiresAt' => $expiresAt];
    }

    public function insertCard(string $imgPath, int $rarityId, int $roleId, int $userId): void {
        $sql = "INSERT INTO cards (path,rarity_id,role_id,added_by) VALUES (?,?,?,?)";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute([$imgPath, $rarityId, $roleId, $userId]);
    }

    public function insertRoom(string $code, int $maxPlayer): int{
        $sql = "INSERT INTO rooms (code,max_player) VALUES (?,?)";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute([$code, $maxPlayer]);
        return (int) $this -> pdo -> lastInsertId();
    }

    public function insertCardsToRoom(int $roomId, array $cardsIds): void{
        $sql = "INSERT INTO room_card (code,max_player) VALUES";
        $values = [];
        foreach ($cardsIds as $cardId) {
            $sql .= " (?, ?)";
            $values[] = $roomId;
            $values[] = $cardId;
        }
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

    private function query(
        string $table, 
        array $conditions = [], 
        array $in = [],
        array $innerJoin = [],
        ?string $orderBy = null, 
        ?int $limit = null, 
        string $fetchMode = 'all'): array|null { 
        $allowed = [
            "rarities" => [],
            "roles" => ["id"],
            "rooms" => ["code", "id"],
            "rememeber_tokens" => ["token_hash"],
            "cards" => ["id", "role_id", "rarity_id"],
            "room_player" => ["users.pseudo", "room_player.room_id"],
            "users" => ["email", "id", "room_player.user_id = users.id", "admin"]
        ];
        if (!array_key_exists($table, $allowed)) throw new InvalidArgumentException("Table non autorisée");

        $sql = "SELECT * FROM $table"; 
        $params = [];

        if (!empty($innerJoin)) {
            foreach ($innerJoin as $joinTable => $joinCondition) {
                if (!array_key_exists($joinTable, $allowed)) throw new InvalidArgumentException("Table de jointure non autorisée");
                if (!in_array($joinCondition, $allowed[$joinTable])) throw new InvalidArgumentException("Condition de jointure non autorisée");
                if (empty($conditions)) throw new InvalidArgumentException("Les jointures nécessitent des conditions pour éviter les résultats en cascade");
                $sql .= " INNER JOIN $joinTable ON $joinCondition";
            }
        }

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

    public function getRememberTokenByTokenHash(string $tokenHash): ?array {
        return $this -> query(
            table: 'remember_tokens',
            conditions: ['token_hash' => $tokenHash],
            limit: 1,
            fetchMode: 'one'
        );
    }

    public function verifyUser(string $email, string $password): false|int {
        $user = $this -> query(
            table: 'users',
            conditions: ['email' => $email],
            fetchMode: 'one'
        );
        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }
        return (int)$user['id'];
    }

    private function getUserById(int $userId): array{
        return $this -> query(
            table: 'users',
            conditions: ['id' => $userId],
            fetchMode: 'one'
        );
    }

    public function isUserAdmin(int $userId): bool {
        $user = $this -> getUserById($userId);
        return (bool)$user['admin'];
    }

    public function getUserPseudo(int $userId): string {
        $user = $this -> getUserById($userId);
        return htmlspecialchars($user['pseudo'], ENT_QUOTES, 'UTF-8');
    }

    public function getRolebyId(int $roleId): array {
        return $this -> query(
            table: 'roles',
            conditions: ['id' => $roleId],
            fetchMode: 'one'
        );
    }

    public function getCardRoles(array $cardsSelected): array {
        $role =  $this -> query(
            table: 'cards',
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

    public function getRoomByCode(string $code): ?array{
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

    public function getPlayersInRoomName(int $roomId): array {
        $result =  $this -> query(
            table: 'room_player',
            innerJoin: ['users' => 'room_player.user_id = users.id'],
            conditions: ['room_player.room_id' => $roomId]
        );
        return array_column($result, 'pseudo');
    }

    public function isGameStarted(int $roomId): bool {
        $room = $this -> query(
            table: 'rooms',
            conditions: ['id' => $roomId],
            limit: 1,
            fetchMode: 'one'
        );
        return (bool)$room['game_started'];
    }

    //todo faire une seul requete pour /Applications/XAMPP/xamppfiles/htdocs/treachery/app/showCard/cardAjax.php
    public function getAllAdminId(){
        $result = $this -> query(
            table: "users",
            conditions: ["admin" => 1]
        );
        return array_column($result, "id");
    }

    public function getMaxPlayerForRoom(int $roomId): int{
        return $this -> query(
            table: "rooms",
            conditions: ["id" => $roomId],
            fetchMode: "one"
        )["max_player"];
    }

}

require_once __DIR__ . "/db/connexion.php";
$db = new DbTools($pdo);
echo "<pre>";
var_dump($db -> insertCardsToRoom(1, [1,2,3]));
echo "</pre>";




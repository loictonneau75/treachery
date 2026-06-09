<?php
namespace App\DB;

use PDO;
use DateTime;
use Exception;

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
        $sql = "INSERT INTO rooms (code, max_player) VALUES (?,?)";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute([$code, $maxPlayer]);
        return (int) $this -> pdo -> lastInsertId();
    }

    public function insertCardsToRoom(int $roomId, array $cardsIds): void{
        $sql = "INSERT INTO room_card (room_id, card_id) VALUES";
        $values = [];
        $placeholdersGroup = [];
        foreach ($cardsIds as $cardId) {
            $placeholdersGroup[] = "(?, ?)";
            $values[] = $roomId;
            $values[] = $cardId;
        }
        $sql .= implode(', ', $placeholdersGroup);
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute($values);
    }

    public function insertPlayerToRoom(int $roomId, int $userId): void {
        try {
            $this -> pdo -> beginTransaction();
            $sql = "UPDATE rooms SET current_players = current_players + 1 WHERE id = ? AND current_players < max_player";
            $stmt = $this -> pdo -> prepare($sql);
            $stmt -> execute([$roomId]);
            if ($stmt -> rowCount() === 0) {
                $this -> pdo -> rollBack();
                throw new Exception("Salon complet");
            }
            $sql = "INSERT INTO room_player (room_id, max_player) VALUES (?, ?)";
            $stmt = $this -> pdo -> prepare($sql);
            $stmt -> execute([$roomId, $userId]);
            $this -> pdo -> commit();
        } catch (Exception $e) {
            if ($this -> pdo -> inTransaction()) $this -> pdo -> rollBack();
            throw $e;
        }
    }

    public function getRememberTokenByTokenHash(string $tokenHash): ?array {
        $sql = "SELECT * FROM remember_tokens WHERE token_hash = ? LIMIT 1";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute([$tokenHash]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function verifyUser(string $email, string $password): false|int {
        $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }
        return (int)$user['id'];
    }

    private function _getUserById(int $userId): array{
        $sql = "SELECT * FROM users WHERE id = ? LIMIT 1";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function isUserAdmin(int $userId): bool {
        return (bool)$this -> _getUserById($userId)['admin'];
    }

    public function getUserPseudo(int $userId): string {
        return htmlspecialchars($this -> _getUserById($userId)['pseudo'], ENT_QUOTES, 'UTF-8');
    }

    public function getRolebyId(int $roleId): array {
        $sql = "SELECT * FROM roles WHERE id = ? LIMIT 1";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute([$roleId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function _fetchRoleIdsByCardIds(array $cardsSelected): array {
        $sql = "SELECT role_id FROM cards WHERE id IN (";
        $sql .= implode(',', array_fill(0, count($cardsSelected), '?'));
        $sql .= ")";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute($cardsSelected);
        return $stmt -> fetchAll(PDO::FETCH_ASSOC);
    }

    public function countRolesInCards(array $cardsSelected): array {
        return array_count_values(array_column($this -> _fetchRoleIdsByCardIds($cardsSelected), 'role_id'));
    }


    public function getCardById(int $id): array {
        $sql = "SELECT * FROM cards WHERE id = ? LIMIT 1";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getRoles(){
        $sql = "SELECT * FROM roles";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute();
        return $stmt -> fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRarities(){
        $sql = "SELECT * FROM rarities";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute();
        return $stmt -> fetchAll(PDO::FETCH_ASSOC);
    }

    private function _getRoomByCode(string $code): ?array{
        $sql = "SELECT * FROM rooms WHERE code = ? LIMIT 1";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute([$code]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getRoomId(string $code): ?int{
        return (int)$this -> _getRoomByCode($code)["id"];
    }

    public function getCardByRoleId(int $roleId): array {
        $sql = "SELECT * FROM cards WHERE role_id = ? ORDER BY rarity_id ASC";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute([$roleId]);
        return $stmt -> fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAccessibleCardsByRoleId(int $roleId, int $userId): array {
        $sql = "SELECT cards.* FROM cards INNER JOIN users ON cards.added_by = users.id
        WHERE cards.role_id = ?
        AND cards.added_by IS NOT NULL
        AND (cards.added_by = ? OR users.admin = 1)
        ORDER BY cards.rarity_id ASC";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute([$roleId, $userId]);
        return $stmt -> fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCardByRarityId(int $rarityId): array {
        $sql = "SELECT * FROM cards WHERE rarity_id = ? ORDER BY role_id ASC";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute([$rarityId]);
        return $stmt -> fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAccessibleCardsByRarityId(int $rarityId, int $userId): array {
        $sql = "SELECT cards.* FROM cards INNER JOIN users ON cards.added_by = users.id
        WHERE cards.rarity_id = ?
        AND cards.added_by IS NOT NULL
        AND (cards.added_by = ? OR users.admin = 1)
        ORDER BY cards.role_id ASC";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute([$rarityId, $userId]);
        return $stmt -> fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPlayersInRoomName(int $roomId): array {
        $sql = "SELECT * FROM room_player INNER JOIN users ON room_player.user_id = users.id WHERE room_player.room_id = ?";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute([$roomId]);
        return array_column($stmt -> fetchAll(PDO::FETCH_ASSOC), 'pseudo');
    }

    private function _getRoomById(int $roomId): array {
        $sql = "SELECT * FROM rooms WHERE id = ? LIMIT 1";
        $stmt = $this -> pdo -> prepare($sql);
        $stmt -> execute([$roomId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function isGameStarted(int $roomId): bool {
        return (bool)$this -> _getRoomById($roomId)['game_started'];
    }

    public function getMaxPlayerForRoom(int $roomId): int{
        return $this -> _getRoomById($roomId)["max_player"];
    }
}

<?php
function userEmailOrPseudoExist(PDO $pdo, string $email, string $pseudo): bool{
    $stmt = $pdo -> prepare("SELECT 1 FROM users WHERE email = ? OR pseudo = ? LIMIT 1");
    $stmt -> execute([$email, $pseudo]);
    return (bool)$stmt->fetch();
}

function createUser(PDO $pdo, string $pseudo, string $email, string $password): int{
    $stmt = $pdo -> prepare("INSERT INTO users (pseudo, email, password) VALUE (?, ?, ?)");
    $stmt -> execute([$pseudo, $email, password_hash($password, PASSWORD_DEFAULT)]);
    return (int)$pdo -> lastInsertId();
}

function getHashByEmail(PDO $pdo, string $email): string|bool {
    $stmt = $pdo->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['password'] ?? false;
}

function getIdByEmail(PDO $pdo, string $email): int{
    $stmt =$pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['id'] ?? false;
}

function getPseudoById(PDO $pdo, int $id): string{
    $stmt =$pdo->prepare("SELECT pseudo FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['pseudo'];
}

function getTypesIdName(PDO $pdo): array {
    $stmt = $pdo->query("SELECT `id`, `name` FROM `type` ORDER BY `name` ASC");
    $res = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $res[(int)$row['id']] = $row['name'];
    }
    return $res;
}


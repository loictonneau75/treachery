<?php

function userExist(PDO $pdo, string $email, string $pseudo): bool{
    $stmt = $pdo -> prepare("SELECT 1 FROM users WHERE email = ? OR pseudo = ? LIMIT 1");
    $stmt -> execute([$email, $pseudo]);
    return (bool)$stmt->fetch();
}

function createUser(PDO $pdo, string $pseudo, string $email, string $password): int{
    $stmt = $pdo -> prepare("INSERT INTO users (pseudo, email, password) VALUE (?, ?, ?)");
    $stmt -> execute([$pseudo, $email, password_hash($password, PASSWORD_DEFAULT)]);
    return (int)$pdo -> lastInsertId();
}
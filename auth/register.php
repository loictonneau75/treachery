<?php
header("Content-Type: application/json; charset=utf-8");
require_once dirname(__DIR__) . "/db/connexion.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Méthode non autorisée');
}

function validEmail(string $email): void{
    if($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
        http_response_code(400);
        exit("Email invalide");
    }
}

function validPseudo(string $pseudo): void{
    if($pseudo === "" || strlen($pseudo) < 3 || strlen($pseudo) > 50){
        http_response_code(400);
        exit("Pseudo invalide");
    }
}

function validPassword(string $password, string $confirm): void{
    if($password === "" || strlen($password) < 5 || strlen($password) > 255 || !preg_match('/^(?=.*[A-Z])(?=.*\d)/', $password)){
        http_response_code(400);
        exit("mot de passe invalide");
    }
    if($password !== $confirm){
        http_response_code(400);
        exit("les mot de passe ne correspondent pas");
    }
}

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

function createSession(int $id, bool $remember): void{
    $_SESSION["id"] = $id;
    if($remember){
        setcookie("id", $id, time() + (30*24*60*60), "/");
    }
}

$errors = [];
$email = trim((string)$_POST["mail-r"]);
$pseudo = trim((string)$_POST["pseudo"]);
$password = trim((string)$_POST["password-r"]);
$confirm = trim((string)$_POST["confirm"]);
$remember = isset($_POST['remember-r']);

validEmail($email);
validPseudo($pseudo);
validPassword($password, $confirm);
if(userExist($pdo, $email, $pseudo)){
    $errors[] = ["Cet email ou ce pseudo sont déjà utilisé", ["mail-r", "pseudo"]];
}
if(!empty($errors)){
    echo json_encode(["valid" => false, "errors" => $errors], JSON_UNESCAPED_UNICODE);
}else{
    $id = createUser($pdo, $pseudo, $email, $password);
    createSession($id, $remember);
    echo json_encode(["valid" => true], JSON_UNESCAPED_UNICODE);
    exit;
}
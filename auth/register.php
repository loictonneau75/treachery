<?php
header("Content-Type: application/json; charset=utf-8");
require_once dirname(__DIR__) . "/db/connexion.php";
require_once dirname(__DIR__) . "/db/utils.php";
require_once dirname(__DIR__) . "/security/utils.php";
require_once dirname(__DIR__) . "/session/utils.php";
session_start();


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

requirePostMethod();
honeyPot($_POST["website-r"]);
rateLimite("register");
throttle("register");

$errors = [];
$email = trim((string)$_POST["mail-r"]);
$pseudo = trim((string)$_POST["pseudo"]);
$password = trim((string)$_POST["password-r"]);
$confirm = trim((string)$_POST["confirm"]);
$remember = isset($_POST['remember-r']);

validEmail($email);
validPseudo($pseudo);
validPassword($password, $confirm);
if(userEmailOrPseudoExist($pdo, $email, $pseudo)){
    $errors[] = ["Cet email ou ce pseudo sont déjà utilisé", ["mail-r", "pseudo"]];
}
if(!empty($errors)){
    echo json_encode(["valid" => false, "errors" => $errors], JSON_UNESCAPED_UNICODE);
}else{
    createSession(createUser($pdo, $pseudo, $email, $password), $remember);
    echo json_encode(["valid" => true], JSON_UNESCAPED_UNICODE);
    exit;
}
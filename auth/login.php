<?php
header("Content-Type: application/json; charset=utf-8");
require_once dirname(__DIR__) . "/db/connexion.php";
require_once dirname(__DIR__) . "/db/utils.php";
require_once dirname(__DIR__) . "/security/utils.php";
require_once dirname(__DIR__) . "/session/utils.php";
session_start();

function validEmail(string $email): void{
    if($email === "") {
        http_response_code(400);
        exit("Email invalide");
    }
}

function validPassword(string $password): void{
    if($password === ""){
        http_response_code(400);
        exit("mot de passe invalide");
    }
}

requirePostMethod();
honeyPot($_POST["website"]);
rateLimite("login");
throttle("login");

$errors = [];
$email = trim((string)$_POST["mail"]);
$password = trim((string)$_POST["password"]);
$remember = isset($_POST['remember']);

validEmail($email);
validPassword($password);
$hash = getHashByEmail($pdo, $email);
if (!($hash && password_verify($password, $hash))) {
    $errors[] = ["Cette combinaison d'identifiant n'existe pas !", ["mail", "password"]];
}
if(!empty($errors)){
    echo json_encode(["valid" => false, "errors" => $errors], JSON_UNESCAPED_UNICODE);
}else{
    createSession(getIdByEmail($pdo, $email), $remember);
    echo json_encode(["valid" => true], JSON_UNESCAPED_UNICODE);
    exit;
}
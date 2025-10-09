<?php
header("Content-Type: application/json; charset=utf-8");
require_once dirname(__DIR__) . "/db/connexion.php";
require_once dirname(__DIR__) . "/db/utils.php";
require_once dirname(__DIR__) . "/security/utils.php";
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

function createSession(int $id, bool $remember): void{
    $_SESSION["id"] = $id;
    if($remember){
        setcookie("id", $id, time() + (30*24*60*60), "/");
    }
}




if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Méthode non autorisée');
}

honeyPot($_POST["website"]);
rateLimite("login");
throttle("login");

$errors = [];
$email = trim((string)$_POST["mail"]);
$password = trim((string)$_POST["password"]);
$remember = isset($_POST['remember-r']);

validEmail($email);
validPassword($password);
$hash = getHashByEmail($pdo, $email);
if (!($hash && password_verify($password, $hash))) {
    $errors[] = ["Cette combinaison d'identifiant n'existe pas !", ["mail", "password"]];
}
if(!empty($errors)){
    echo json_encode(["valid" => false, "errors" => $errors], JSON_UNESCAPED_UNICODE);
}else{
    $id = getIdByEmail($pdo, $email);
    createSession($id, $remember);
    echo json_encode(["valid" => true], JSON_UNESCAPED_UNICODE);
    exit;
}
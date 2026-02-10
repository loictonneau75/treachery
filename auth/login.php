<?php
use App\DB\DbTools;
use App\Session\SessionTools;
use App\Security\CsrfTools;
use App\Security\FormSecurity;

header("Content-Type: application/json; charset=utf-8");
require_once dirname(__DIR__) . "/db/connexion.php";
require_once dirname(__DIR__) . "/db/tools.php";
require_once dirname(__DIR__) . "/session/tools.php";
require_once dirname(__DIR__) . "/security/tools.php";

function validEmail(string $email): void{
    $maxlength = 255;
    if($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > $maxlength) {
        http_response_code(400);
        exit("Email invalide");
    }
}

function validPassword(string $password): void{
    $minLength = 5;
    $maxLength = 255;
    $passwordRegex = "/(?=.*[A-Z])(?=.*\d)/";
    if($password === "" || strlen($password) < $minLength || strlen($password) > $maxLength || !preg_match($passwordRegex, $password)){
        http_response_code(400);
        exit("mot de passe invalide");
    }
}
SessionTools::sessionStart();
FormSecurity::protectForm("login", $_POST['hp_email'] ?? null, $_POST['csrf_token'] ?? null);
$errors = [];
$email = trim((string)$_POST["mailLogin"]);
$password = trim((string)$_POST["passwordLogin"]);
$remember = isset($_POST["rememberLogin"]);

validEmail($email);
validPassword($password);
$id = DbTools::verifyUser($pdo, $email, $password);
if($id === false){
    echo json_encode([
        'valid'  => false,
        'errors' => [["email ou mot de passe invalide", ["mailLogin", "passwordLogin"]]]
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
SessionTools::createSession($pdo,$id, $remember);
CsrfTools::regenerateToken();
echo json_encode(["valid" => true], JSON_UNESCAPED_UNICODE);
exit;


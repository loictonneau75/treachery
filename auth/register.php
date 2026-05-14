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

function validPseudo(string $pseudo): void{
    $minLength = 3;
    $maxLength = 50;
    if($pseudo === "" || strlen($pseudo) < $minLength || strlen($pseudo) > $maxLength){
        http_response_code(400);
        exit("Pseudo invalide");
    }
}

function validPassword(string $password, string $confirm): void{
    $minLength = 5;
    $maxLength = 255;
    $passwordRegex = "/(?=.*[A-Z])(?=.*\d)/";
    if($password === "" || strlen($password) < $minLength || strlen($password) > $maxLength || !preg_match($passwordRegex, $password)){
        http_response_code(400);
        exit("mot de passe invalide");
    }
    if($password !== $confirm){
        http_response_code(400);
        exit("les mot de passe ne correspondent pas");
    }
}

FormSecurity::protectForm("login", $_POST['hp_email'] ?? null, $_POST['csrf_token'] ?? null);
$db = new DbTools($pdo);
$session = new SessionTools($db);

$email = trim((string)$_POST["mailRegister"]);
$pseudo = trim((string)$_POST["pseudoRegister"]);
$password = trim((string)$_POST["passwordRegister"]);
$confirm = trim((string)$_POST["confirmRegister"]);
$remember = isset($_POST["rememberRegister"]);

validEmail($email);
validPseudo($pseudo);
validPassword($password, $confirm);
if($db -> userEmailExist($email, $pseudo)){
    echo json_encode([
        'valid'  => false,
        'errors' => [['Identifiants invalides', ['mailRegister', 'pseudoRegister', 'passwordRegister', 'confirmRegister']]]
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$id = $db -> createUser($pseudo, $email, $password);
$session -> createSession($id, $remember);
CsrfTools::regenerateToken();
echo json_encode(["valid" => true], JSON_UNESCAPED_UNICODE);
exit;

<?php
use App\DB\DbTools;
use App\Session\SessionTools;
use App\Security\FormSecurity;

header("Content-Type: application/json; charset=utf-8");
require_once dirname(__DIR__,2) . "/db/connexion.php";
require_once dirname(__DIR__,2) . "/db/tools.php";
require_once dirname(__DIR__,2) . "/security/tools.php";
require_once dirname(__DIR__,2) . "/session/tools.php";

function getPostInt(string $key): int {
    $value = filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT);
    if ($value === false || $value === null) {
        echo json_encode([
            'valid'  => false,
            'errors' => [["Valeur invalide pour $key", [$key]]]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    return $value;
}

function validateCardRole(PDO $pdo, int $id): void{
    if (!DbTools::recordExists($pdo, "roles", $id)){
        echo json_encode([
            'valid'  => false,
            'errors' => [["Rôle invalide", ["cardRoleInput"]]]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function validateCardRarity(PDO $pdo, int $id): void{
    if(!DbTools::recordExists($pdo, "rarities", $id)){
        echo json_encode([
            'valid'  => false,
            'errors' => [["Rareté invalide", ["cardRarityInput"]]]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function validateCardImg(array $img, $allowedExtension): string{
    if($img === "" ){
        echo json_encode([
            'valid'  => false,
            'errors' => [["Image invalide", ["cardImg"]]]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $extension = FormSecurity::validateFile($img, $allowedExtension, 2 * 1024 * 1024);
    return bin2hex(random_bytes(16)) . '.' . $extension;
}

function checkSession(): int{
    $id = SessionTools::getData("id");
    if($id === null){
        http_response_code(401);
        exit("Session invalide, veuillez vous reconnecter");
    }
    return (int)$id;
}

SessionTools::sessionStart();
FormSecurity::protectForm("addCard", $_POST['hp_email'] ?? null, $_POST['csrf_token'] ?? null);
$role = getPostInt("cardRole");
$rarity = getPostInt("cardRarity");
$img = $_FILES["cardImg"];

validateCardRole($pdo, $role);
validateCardRarity($pdo, $rarity);
$path = validateCardImg($img, ["png", "jpg"]);
$id = checkSession();
DbTools::createCard($pdo, $path, $rarity, $role, $id);
move_uploaded_file($img['tmp_name'], dirname(__DIR__,2) . "/assets/img/cards/" . $path);
echo json_encode(["valid" => true], JSON_UNESCAPED_UNICODE);
exit;
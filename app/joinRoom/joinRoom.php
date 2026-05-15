<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
use App\Security\FormSecurity;
use App\Session\SessionTools;
use App\DB\DbTools;

require_once dirname(__DIR__,2) . "/security/tools.php";
require_once dirname(__DIR__,2) . "/session/tools.php";
require_once dirname(__DIR__,2) . "/db/connexion.php";
require_once dirname(__DIR__,2) . "/db/tools.php";


header("Content-Type: application/json; charset=utf-8");

FormSecurity::protectForm("joinRoom", $_POST['hp_email'] ?? null, $_POST['csrf_token'] ?? null);
$db = new DbTools($pdo);
$session = new SessionTools($db);

$code = trim((string)$_POST["code"]);
if (empty($code)){
    echo json_encode([
        'valid'  => false,
        'errors' => [["Le code est requis", ["code"]]]
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
if (!$db -> existsRoom($code)){
    echo json_encode([
        'valid'  => false,
        'errors' => [["Le code n'existe pas", ["code"]]]
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$roomId = $db -> getRoomByCode($code);
$userId = SessionTools::getData("id");

if ($db -> existsUserInRoom($userId, $roomId)) {
        echo json_encode(['valid' => true, "code" => $code], JSON_UNESCAPED_UNICODE);
        exit;
    }

try {
    $db -> insertPlayerToRoom($roomId, $userId);
    echo json_encode(['valid' => true, "code" => $code], JSON_UNESCAPED_UNICODE);
    exit;
} catch (Exception $e) {
    echo json_encode(['valid' => false, 'errors' => [["Impossible de rejoindre le salon", ["code"]]]], JSON_UNESCAPED_UNICODE);
    exit;
}
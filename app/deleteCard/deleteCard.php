<?php
use App\Session\SessionTools;
use App\Security\CsrfTools;
use App\DB\DbTools;

require_once dirname(__DIR__,2) . "/session/tools.php";
require_once dirname(__DIR__,2) . "/security/tools.php";
require_once dirname(__DIR__,2) . "/db/connexion.php";
require_once dirname(__DIR__,2) . "/db/tools.php";

header('Content-Type: application/json');
SessionTools::sessionStart();
$data = json_decode(file_get_contents("php://input"), true);
CsrfTools::validateToken($data["csrf_token"]);
$db = new DbTools($pdo);

$card = $db -> getById('cards', $data['card']['id']);
if (!$card) {
    echo json_encode(["success" => false, "message" => "Carte introuvable"]);
    exit;
}

//todo voir pour enlever le (bool)(int)
if ($card['added_by'] != SessionTools::getData("id") && !(bool)(int)$db -> getFieldById("users", "is_admin", SessionTools::getData("id"))) {
    echo json_encode(["success" => false, "message" => "Action interdite"]);
    exit;
}

unlink(dirname(__DIR__,2) . "/assets/img/cards/" . $card["path"]);
$db -> deleteById('cards', $card['id']);
echo json_encode(["success" => true]);

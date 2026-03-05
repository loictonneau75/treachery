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
error_log((bool)(int)DbTools::getFieldById($pdo, "users", "is_admin", SessionTools::getData("id")));
$data = json_decode(file_get_contents("php://input"), true);
CsrfTools::validateToken($data["csrf_token"]);
$card = DbTools::getById($pdo, 'cards', $data['card']['id']);
if (!$card) {
    echo json_encode(["success" => false, "message" => "Carte introuvable"]);
    exit;
}
if ($card['added_by'] != SessionTools::getData("id") && !(bool)(int)DbTools::getFieldById($pdo, "users", "is_admin", SessionTools::getData("id"))) {
    echo json_encode(["success" => false, "message" => "Action interdite"]);
    exit;
}

unlink(dirname(__DIR__,2) . "/assets/img/cards/" . $card["path"]);
DbTools::deleteById($pdo, 'cards', $card['id']);
echo json_encode(["success" => true]);

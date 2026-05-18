<?php
use App\Session\SessionTools;
use App\Security\CsrfTools;
use App\DB\DbTools;

require_once dirname(__DIR__,2) . "/session/tools.php";
require_once dirname(__DIR__,2) . "/security/tools.php";
require_once dirname(__DIR__,2) . "/db/connexion.php";
require_once dirname(__DIR__,2) . "/db/tools.php";

header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);
$db = new DbTools($pdo);
$session = new SessionTools($db);
CsrfTools::validateToken($data["csrf_token"]);

$card = $db -> getCardById($data['card']['id']);
if (!$card) {
    echo json_encode(["success" => false, "message" => "Carte introuvable"]);
    exit;
}

if ($card['added_by'] != SessionTools::getData("id") && !$db -> isUserAdmin(SessionTools::getData("id"))) {
    echo json_encode(["success" => false, "message" => "Action interdite"]);
    exit;
}

$db -> deleteCardById($card['id']);
unlink(dirname(__DIR__,2) . "/assets/img/cards/" . $card["path"]);
echo json_encode(["success" => true]);

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
$table = $data['groupBy'] === "role" ? "roles" : "rarities";
$order= $data['groupBy'] === "rarity" ? "role_id" : "rarity_id";
$groups = DbTools::getAllFrom($pdo, $table);
$groupedData = [];
foreach ($groups as $group) {
    $cards = DbTools::getCardsBy($pdo, [$data['groupBy'] . "_id" => $group['id']], $order);
    $groupedData[$group['id']] = ["info"  => $group, "cards" => $cards];
}

echo json_encode(["groups" => $groupedData]);

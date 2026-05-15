<?php
use App\Session\SessionTools;
use App\Security\CsrfTools;
use App\DB\DbTools;

require_once dirname(__DIR__,2) . "/session/tools.php";
require_once dirname(__DIR__,2) . "/security/tools.php";
require_once dirname(__DIR__,2) . "/db/connexion.php";
require_once dirname(__DIR__,2) . "/db/tools.php";

function getJsonInput(): array {
    $input = file_get_contents("php://input");
    return json_decode($input, true);
}

function buildGroupedData(DbTools $db, string $groupBy): array{
    $groupedData = [];
    //todo voir pour simplifié
    foreach ($groupBy === "role" ? $db -> getRoles() : $db -> getRarities() as $group) {
        $cards = $groupBy === "role" ? $db -> getCardByRoleId($group['id']) : $db -> getCardByRarityId($group['id']);
        $groupedData[$group['id']] = ["info"  => $group,"cards" => $cards];
    }
    return $groupedData;
}

header('Content-Type: application/json');

$data = getJsonInput();
CsrfTools::validateToken($data["csrf_token"]);
$db = new DbTools($pdo);
$session = new SessionTools($db);

$groupedData = buildGroupedData($db, $data['groupBy']);
$id = SessionTools::getData("id");
echo json_encode(["groups" => $groupedData, "id" => $id, "admin" => $db -> isUserAdmin($id)]);

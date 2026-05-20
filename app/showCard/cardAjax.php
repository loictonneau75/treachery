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

function buildGroupedData(DbTools $db, string $groupBy, bool $isAdmin): array{
    $groupedData = [];
    $isRoleGroup = $groupBy === "role";
    $groups = $isRoleGroup ? $db->getRoles() : $db->getRarities();
    $userId = SessionTools::getData("id");
    $allowed = [...$db->getAllAdminId(), $userId];
    foreach ($groups as $group) {
        $cards = $isRoleGroup ? $db->getCardByRoleId($group['id']) : $db->getCardByRarityId($group['id']);
        if (!$isAdmin) {
            $cards = array_values(array_filter($cards, fn($card) => in_array($card['added_by'], $allowed, true)));
        }
        $groupedData[$group['id']] = ["info"  => $group, "cards" => $cards];
    }
    return $groupedData;
}

header('Content-Type: application/json');

$data = getJsonInput();
$db = new DbTools($pdo);
$session = new SessionTools($db);
CsrfTools::validateToken($data["csrf_token"]);
$id = SessionTools::getData("id");
$isAdmin = $db -> isUserAdmin($id);
$groupedData = buildGroupedData($db, $data['groupBy'], $isAdmin);
echo json_encode(["groups" => $groupedData, "id" => $id, "admin" => $isAdmin]);

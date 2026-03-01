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

function resolveGroupingConfig(string $groupBy): array{
    $table = $groupBy === "role" ? "roles" : "rarities";
    $order = $groupBy === "rarity" ? "role_id" : "rarity_id";
    return [$table, $order];
}

function buildGroupedData(PDO $pdo, array $groups, string $groupBy, string $order): array{
    $groupedData = [];
    foreach ($groups as $group) {
        $cards = DbTools::getCardsBy($pdo,[$groupBy . "_id" => $group['id']],$order);
        $groupedData[$group['id']] = ["info"  => $group,"cards" => $cards];
    }
    return $groupedData;
}


header('Content-Type: application/json');
SessionTools::sessionStart();
$data = getJsonInput();
CsrfTools::validateToken($data["csrf_token"]);
[$table, $order] = resolveGroupingConfig($data['groupBy']);
$groupedData = buildGroupedData($pdo, DbTools::getAllFrom($pdo, $table), $data['groupBy'], $order);
$id = SessionTools::getData("id");
echo json_encode(["groups" => $groupedData, "id" => $id, "admin" => (bool) DbTools::getFieldById($pdo, "users", "is_admin", $id)]);

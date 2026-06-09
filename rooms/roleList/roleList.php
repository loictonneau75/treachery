<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
use App\Rules\RoleRules;
use App\DB\DbTools;

require_once dirname(__DIR__,2) . "/db/connexion.php";
require_once dirname(__DIR__,2) . "/db/tools.php";
require_once dirname(__DIR__,2) . "/rules/rules.php";

header('Content-Type: application/json');
$db = new DbTools($pdo);

$roles = [];
foreach(RoleRules::getRoleDistribution($db -> getMaxPlayerForRoom($db -> getRoomId($_GET["code"]))) as $roleId => $count){
    $roles[$roleId] = [...$db -> getRolebyId($roleId), "count" => $count];
};

echo json_encode(["roles" => $roles]);
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
use App\DB\DbTools;

require_once dirname(__DIR__,2) . "/db/connexion.php";
require_once dirname(__DIR__,2) . "/db/tools.php";

header('Content-Type: application/json');
$db = new DbTools($pdo);

$roomId = $db -> getRoomId($_GET["code"]);
echo json_encode(["playerName" => $db -> getPlayersInRoomName($roomId), "started" => $db -> isGameStarted($roomId)]);
?>
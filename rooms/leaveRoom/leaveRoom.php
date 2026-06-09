<?php
header('Content-Type: application/json');
use App\Session\SessionTools;
use App\DB\DbTools;

require_once dirname(__DIR__,2) . "/config.php";
require_once dirname(__DIR__,2) . "/session/tools.php";
require_once dirname(__DIR__,2) . "/db/connexion.php";
require_once dirname(__DIR__,2) . "/db/tools.php";

$db = new DbTools($pdo);
$session = new SessionTools($db);
$roomId = $db -> getRoomId($_GET["code"]);
$db -> deleteUserFromRoom(SessionTools::getData("id"), $roomId);

echo json_encode(["success" => true, "redirect" => BASE_URL . "/index.php"]);

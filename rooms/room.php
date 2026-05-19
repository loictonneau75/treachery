<?php
ini_set('display_errors', 1);
use App\Session\SessionTools;
use App\DB\DbTools;


require_once dirname(__DIR__) . "/config.php";
require_once dirname(__DIR__) . "/db/connexion.php";
require_once dirname(__DIR__) . "/db/tools.php";
require_once dirname(__DIR__). "/session/tools.php";

$roomCode = $_GET["code"] ?? null;
$pageType = "room";
$pageName = "Salon " . $roomCode ." - ". TITLE;

$db = new DbTools($pdo);
$session = new SessionTools($db);
$userId = SessionTools::getData("id");
$roomId = $db -> getRoomByCode("$roomCode")["id"];

if (!$userId || !$roomCode || !$roomId || !$db -> existsUserInRoom($userId, $roomId)) {
    header("Location: " . BASE_URL);
    exit();
}


include dirname(__DIR__) . "/partial/header.php";
include dirname(__DIR__) . "/navbar/navbar.php";

?>
<h1>Salon <?=$roomCode?></h1>
<!--todo voir pour utiliser websocket -->
<div id="players">
    <h2>Joueur connecté</h2>
</div>
<div id="roles">
    <h2>Rôles disponibles</h2>
</div>
<div id="card">
    <h2>Cartes Séléctionnées</h2>
</div>


<?php
include dirname(__DIR__) . "/partial/footer.php";
?>
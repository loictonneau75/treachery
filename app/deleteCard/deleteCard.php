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
$card = $data['card'];
error_log(print_r($card, true));


// $stmt = $pdo->prepare("SELECT added_by FROM cards WHERE id = ?");
// $stmt->execute([$cardId]);
// $card = $stmt->fetch(PDO::FETCH_ASSOC);

// if (!$card) {
//     echo json_encode(["success" => false, "message" => "Carte introuvable"]);
//     exit;
// }

// if ($card['added_by'] != $_SESSION['ID']) {
//     echo json_encode(["success" => false, "message" => "Action interdite"]);
//     exit;
// }

// /* Suppression */
// $stmt = $pdo->prepare("DELETE FROM cards WHERE id = ?");
// $stmt->execute([$cardId]);

// echo json_encode(["success" => true]);

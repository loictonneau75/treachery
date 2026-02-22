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

$group = DbTools::getAllFrom($pdo, $data['groupBy']);

echo json_encode(["group" => $group]);

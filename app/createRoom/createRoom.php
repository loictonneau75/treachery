<?php
use App\Security\FormSecurity;
use App\Session\SessionTools;
use App\DB\DbTools;
use App\Rules\RoleRules;

require_once dirname(__DIR__,2) . "/security/tools.php";
require_once dirname(__DIR__,2) . "/session/tools.php";
require_once dirname(__DIR__,2) . "/db/connexion.php";
require_once dirname(__DIR__,2) . "/db/tools.php";
require_once dirname(__DIR__,2) . "/rules/rules.php";


header("Content-Type: application/json; charset=utf-8");
function validateNumberPlayers(int|false $nbPlayerinRoom, int $minNbPlayerAllowedInRoom, int $maxNbPlayerAllowedInRoom): void{
    if ($nbPlayerinRoom === false) {
        echo json_encode([
            'valid'  => false,
            'errors' => [["Nombre de joueurs invalide", ["nbPlayers"]]]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($nbPlayerinRoom < $minNbPlayerAllowedInRoom || $nbPlayerinRoom > $maxNbPlayerAllowedInRoom){
        echo json_encode([
            'valid'  => false,
            'errors' => [["Le nombre de joueur doit être compris entre $minNbPlayerAllowedInRoom et $maxNbPlayerAllowedInRoom", ["nbPlayers"]]]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function validateAmountOfCardsSelected(array $cardsSelected, int $nbPlayerinRoom): void{
    if (count($cardsSelected) < $nbPlayerinRoom){
        echo json_encode([
            'valid'  => false,
            'errors' => [["Vous devez sélectionner au moins autant de cartes que de joueurs", ["cardIds"]]]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function validateAmountOfCardByRoles(DbTools $db, int $nbPlayerinRoom, array $cardsSelected): void{
    foreach (RoleRules::getRoleDistribution($nbPlayerinRoom) as $roleId => $requiredNumber){
        if ($db -> countRolesInCards($cardsSelected)[$roleId] < $requiredNumber){
            echo json_encode([
                'valid'  => false,
                'errors' => [["Vous devez sélectionner au moins $requiredNumber cartes du rôle " . $db -> getRolebyId($roleId)["name"], ["fakeCheckboxSelectAllCard"]]]
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
}

function generateCode(int $length) {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $code;
}

function findNewRoomCode(DbTools $db): string{
    do {
        $code = generateCode(5);
    } while ($db -> existsRoom($code));
    return $code;
}


$db = new DbTools($pdo);
$session = new SessionTools($db);
FormSecurity::protectForm("createRoom", $_POST['hp_email'] ?? null, $_POST['csrf_token'] ?? null);

[$minNbPlayerAllowedInRoom, $maxNbPlayerAllowedInRoom] = json_decode($_POST["minMax"], true);
$nbPlayerinRoom = filter_var($_POST["nbPlayers"], FILTER_VALIDATE_INT);
$cardsSelected = json_decode($_POST["cardIds"], true);
validateNumberPlayers($nbPlayerinRoom, $minNbPlayerAllowedInRoom, $maxNbPlayerAllowedInRoom);
validateAmountOfCardsSelected($cardsSelected, $nbPlayerinRoom);
validateAmountOfCardByRoles($db, $nbPlayerinRoom, $cardsSelected);
$code = findNewRoomCode($db);
$roomId = $db -> insertRoom($code, $nbPlayerinRoom);
$db -> insertCardsToRoom($roomId, $cardsSelected);
$db -> insertPlayerToRoom($roomId, SessionTools::getData("id"));
echo json_encode(["valid" => true, "code" => $code], JSON_UNESCAPED_UNICODE);
exit;

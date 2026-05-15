<?php
use App\Security\FormSecurity;
use App\Session\SessionTools;
use App\DB\DbTools;

require_once dirname(__DIR__,2) . "/security/tools.php";
require_once dirname(__DIR__,2) . "/session/tools.php";
require_once dirname(__DIR__,2) . "/db/connexion.php";
require_once dirname(__DIR__,2) . "/db/tools.php";


header("Content-Type: application/json; charset=utf-8");
//todo typer
function validateNunberPlayers($nbPlayers, $min, $max): void{
    if ($nbPlayers < $min || $nbPlayers > $max){
        echo json_encode([
            'valid'  => false,
            'errors' => [["Le nombre de joueur doit être compris entre $min et $max", ["nbPlayers"]]]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
//todo typer
function validateAmountOfCardsSelected($cardsSelected, $nbPlayers): void{
    if (count($cardsSelected) < $nbPlayers){
        echo json_encode([
            'valid'  => false,
            'errors' => [["Vous devez sélectionner au moins autant de cartes que de joueurs", ["cardIds"]]]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function getRoleDistribution(int $nbPlayers): array {
    return match($nbPlayers) {
        4 => [1 => 1, 4 => 1, 3 => 2, 2 => 0],
        5 => [1 => 1, 4 => 1, 3 => 2, 2 => 1],
        6 => [1 => 1, 4 => 1, 3 => 3, 2 => 1],
        7 => [1 => 1, 4 => 1, 3 => 3, 2 => 2],
        8 => [1 => 1, 4 => 2, 3 => 3, 2 => 2],
        default => []
    };
}
//todo typer
function validateAmountOfCardByRoles(DbTools $db, $nbPlayers, $cardsSelected): void{
    foreach (getRoleDistribution($nbPlayers) as $roleId => $required){
        if (array_count_values($db -> getCardRoles($cardsSelected))[$roleId] < $required){
            echo json_encode([
                'valid'  => false,
                'errors' => [["Vous devez sélectionner au moins $required cartes du rôle " . $db -> getRoleName($roleId), ["fakeCheckboxSelectAllCard"]]]
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
}

//todo typer
function generateCode($length) {
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


FormSecurity::protectForm("createRoom", $_POST['hp_email'] ?? null, $_POST['csrf_token'] ?? null);
$db = new DbTools($pdo);
$session = new SessionTools($db);

[$min, $max] = json_decode($_POST["minMax"], true);
$nbPlayers = $_POST["nbPlayers"];
$cardsSelected = json_decode($_POST["cardIds"], true);
validateNunberPlayers($nbPlayers, $min, $max);
validateAmountOfCardsSelected($cardsSelected, $nbPlayers);
validateAmountOfCardByRoles($db, $nbPlayers, $cardsSelected);
$code = findNewRoomCode($db);
$roomId = $db -> insertRoom($code, $nbPlayers);
foreach ($cardsSelected as $cardId) {
    $db -> insertCardToRoom($roomId, $cardId);
}
$db -> insertPlayerToRoom($roomId, SessionTools::getData("id"));
echo json_encode(["valid" => true, "code" => $code], JSON_UNESCAPED_UNICODE);
exit;

<?php
require_once dirname(__DIR__,2) . "/db/connexion.php";
require_once dirname(__DIR__,2) . "/db/utils.php";

function getCardRarity($pdo){ 
    preg_match_all("/'([^']+)'/", (getAllCollumnFromTable($pdo, 'cards', 'rarity')), $matches); 
    return $matches[1]; 
}
?>
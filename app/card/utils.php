<?php
require_once dirname(__DIR__,2) . "/db/connexion.php";
require_once dirname(__DIR__,2) . "/db/utils.php";

function getRolesTypes($pdo){
    preg_match_all("/'([^']+)'/", (getAllCollumnFromTable($pdo, 'cards', 'types'))['Type'], $matches);
    return $matches[1];
}

?>
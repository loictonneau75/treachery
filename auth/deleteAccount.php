<?php

use App\DB\DbTools;
use App\Session\SessionTools;
use App\Security\FormSecurity;

require_once dirname(__DIR__) . "/db/connexion.php";
require_once dirname(__DIR__) . "/db/tools.php";
require_once dirname(__DIR__) . "/session/tools.php";
require_once dirname(__DIR__) . "/security/tools.php";
require_once dirname(__DIR__) . "/config.php";

FormSecurity::protectForm("delete_account", $_POST["hp_email"] ?? null, $_POST["csrf_token"] ?? null);
$db = new DbTools($pdo);
$session = new SessionTools($db);

if (SessionTools::getData("id") === null) {
    http_response_code(403);
    exit("Utilisateur non authentifié");
}
$db -> deleteUserById((int)SessionTools::getData("id"));
if (isset($_COOKIE['remember_me'])) {
    $db -> deleteRememberTokensByTokenHash(hash('sha256', $_COOKIE['remember_me']));
    SessionTools::clearRememberCookie();
}
SessionTools::deleteSession();
header("Location: " . BASE_URL);
exit;

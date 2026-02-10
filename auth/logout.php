<?php
use App\DB\DbTools;
use App\Session\SessionTools;

require_once dirname(__DIR__) . "/db/connexion.php";
require_once dirname(__DIR__) . "/db/tools.php";
require_once dirname(__DIR__) . "/session/tools.php";
require_once dirname(__DIR__) . "/config.php";

SessionTools::sessionStart();

if (isset($_COOKIE['remember_me'])) {
    DbTools::deleteRememberTokenForHash($pdo, hash('sha256', $_COOKIE['remember_me']));
    SessionTools::clearRememberCookie();
}

SessionTools::deleteSession();
header("Location: " . BASE_URL);
exit;

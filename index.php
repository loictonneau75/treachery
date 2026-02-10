<?php
use App\Session\SessionTools;
use App\Security\CsrfTools;

require_once __DIR__ . "/config.php";
require_once __DIR__ . "/db/connexion.php";
require_once __DIR__ . "/db/tools.php";
require_once __DIR__ . "/session/tools.php";
require_once __DIR__ . "/security/tools.php";

SessionTools::sessionStart();

SessionTools::autoLogin($pdo);
CsrfTools::generateToken();

include __DIR__ . "/partial/header.php";

include SessionTools::getData('id') ? __DIR__ . "/app/app.php" : __DIR__ . "/auth/auth.php";

include __DIR__ . "/partial/footer.php";
?>
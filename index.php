<?php 
define("BASE_PATH", __DIR__ . "/");
define("BASE_URL", "http://localhost/treachery/");
define("TITLE", "Draw Your Fate");

$loggedIn = false;

include BASE_PATH . "partial/header.php";

include $loggedIn ? BASE_PATH . "app/app.php" : BASE_PATH . "auth/auth.php";

include BASE_PATH . "partial/footer.php";
?>

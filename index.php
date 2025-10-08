<?php 
require_once __DIR__ . '/config.php';

$loggedIn = false;

include __DIR__ . "/partial/header.php";

include $loggedIn ? __DIR__ . "/app/app.php" : __DIR__ . "/auth/auth.php";

include __DIR__ . "/partial/footer.php";
?>

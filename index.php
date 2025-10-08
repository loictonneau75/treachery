<?php

session_start();
require_once __DIR__ . '/config.php';

if(!isset($_SESSION["id"]) && isset($_COOKIE["id"])){
    $_SESSION["id"] = $_COOKIE["id"];
}
$loggedIn = isset($_SESSION["id"]);

include __DIR__ . "/partial/header.php";

include $loggedIn ? __DIR__ . "/app/app.php" : __DIR__ . "/auth/auth.php";

include __DIR__ . "/partial/footer.php";
?>

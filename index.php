<?php 
$title = "Draw Your Fate";
$loggedIn = false;

include "partial/header.php";

include $loggedIn ? "app/app.php" : "auth/auth.php";


include "partial/footer.php";
?>

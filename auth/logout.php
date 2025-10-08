<?php
require_once dirname(__DIR__) . "/config.php";

session_start();
if (isset($_COOKIE["id"])) {
    setcookie("id", $_SESSION["id"], time() - 36000, "/");
    unset($_COOKIE["id"]);
}
session_unset();
session_destroy();
header("Location: " . BASE_URL);



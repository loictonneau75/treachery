<?php
use App\DB\DbTools;
use App\Session\SessionTools;
use App\Security\CsrfTools;
use App\Security\FormSecurity;

header("Content-Type: application/json; charset=utf-8");
require_once dirname(__DIR__) . "/db/connexion.php";
require_once dirname(__DIR__) . "/db/tools.php";
require_once dirname(__DIR__) . "/session/tools.php";
require_once dirname(__DIR__) . "/security/tools.php";

SessionTools::sessionStart();
FormSecurity::protectForm("addCard", $_POST['hp_email'] ?? null, $_POST['csrf_token'] ?? null);
$errors = [];
$role = trim((string)$_POST["role"]);
$rarity = trim((string)$_POST["rarity"]);
$img = trim($_FILES["img"]);
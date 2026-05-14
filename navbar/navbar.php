<?php
use App\DB\DbTools;
use App\Session\SessionTools;

require_once dirname(__DIR__) . "/db/connexion.php";
require_once dirname(__DIR__) . "/db/tools.php";
require_once dirname(__DIR__) . "/session/tools.php";
require_once dirname(__DIR__) . "/config.php";

$db = new DbTools($pdo);
?>

<nav>
    <div>
        <img src="<?=BASE_URL?>assets/img/logo_horizontal.png" alt="img/logo_horizontal.png">
        <div>
            <h1><?=htmlspecialchars($db -> getFieldById("users", "pseudo", SessionTools::getData("id")), ENT_QUOTES, 'UTF-8');?></h1>
            <button class="burger"><span></span><span></span><span></span></button>
        </div>
    </div>
    <div id="dropdown" hidden>
        <a href = "<?=BASE_URL?>auth/logout.php">Se deconnecter</a>
        <a href = "#" id="deleteAccountBtn">Suprimer son compte</a>
    </div>
    <form id="deleteAccountForm" method="POST" action="<?=BASE_URL?>auth/deleteAccount.php" hidden>
        <input type="hidden" name="csrf_token" value="<?=SessionTools::getData("csrf_token")?>">
        <input type="text" name="hp_email" style="display:none" autocomplete="off">
    </form>
</nav>
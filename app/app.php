<?php
require_once dirname(__DIR__) ."/db/connexion.php";
require_once dirname(__DIR__) ."/db/utils.php";
require_once __DIR__. "/card/utils.php";
include __DIR__ . "/navbar/navbar.php";
?>

<div class="row">
    <?php
        include __DIR__ . "/partial/joinRoomForm.php";
        include __DIR__ . "/partial/createRoomForm.php";
    ?>
</div>
<div class="row alone blur">
    <?php
        include __DIR__ . "/partial/addCardForm.php";
    ?>
</div>
<div class="row">


</div>
<div class="test"></div>
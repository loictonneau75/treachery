<?php
use App\Session\SessionTools;

require_once dirname(__DIR__, 2) . "/session/tools.php";
?>

<form action="<?=BASE_URL?>app/partial/joinRoom/joinRoom.php" id="joinRoomForm" class="element">
    <h2>Rejoindre un salon</h2>
    <div class="input-wrapper">
        <input type="text" id="code" placeholder="">
        <label for="code">Code</label>
    </div>
    <input type="hidden" name="csrf_token" value="<?=SessionTools::getData("csrf_token")?>">
    <input type="text" name="hp_email" style="display:none" autocomplete="off">
    <button>Rejoindre</button>
</form>
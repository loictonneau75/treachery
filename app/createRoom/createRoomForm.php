<?php
use App\Session\SessionTools;

require_once dirname(__DIR__, 2) . "/session/tools.php";
?>

<form action="<?=BASE_URL?>app/partial/createRoom/createRoom.php" id="createRoomForm">
    <h2>Rejoindre un salon</h2>
    <div>
        <input type="text" id="code">
        <label for="code">Code</label>
    </div>
    <input type="hidden" name="csrf_token" value="<?=SessionTools::getData("csrf_token")?>">
    <input type="text" name="hp_email" style="display:none" autocomplete="off">
    <button>Rejoindre</button>
</form>
<?php
use App\Session\SessionTools;
use App\CustomInputNumber\CustomInputNumber;

require_once dirname(__DIR__, 2) . "/session/tools.php";
require_once dirname(__DIR__) . "/customInputNumber/customInputNumber.php";
?>

<form action="<?=BASE_URL?>app/partial/createRoom/createRoom.php" id="createRoomForm" class="element">
    <h2>Creer un salon</h2>
    <div class="input-number-wrapper">
        <?=CustomInputNumber::renderCustomInputNumber()?>
        <label for="nbPlayers">nombre de joueur</label>
    </div>
    <div class="custom-checkbox">
        <input type="checkbox" id="selectAllCard" name="selectAllCard">
        <label for="selectAllCard" class="toggle"></label>
        <label for="selectAllCard">Sélectionner toutes les cartes</label>
    </div>
    <input type="hidden" name="csrf_token" value="<?=SessionTools::getData("csrf_token")?>">
    <input type="text" name="hp_email" style="display:none" autocomplete="off">
    <button>Créer</button>
</form>



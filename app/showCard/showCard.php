<?php
use App\Session\SessionTools;

require_once dirname(__DIR__,2) . "/session/tools.php";
?>

<div id="showCard">
    <div>
        <input type="radio" name="groupBy" value="roles" data-action ="<?=BASE_URL?>app/showCard/cardAjax.php" checked>
        <label for="role">Rôle</label>

        <input type="radio" name="groupBy" value="rarities" data-action ="<?=BASE_URL?>app/showCard/cardAjax.php">
        <label for="rarity">Rareté</label>
    </div>

    <input type="hidden" name="csrfToken" value="<?=SessionTools::getData("csrf_token")?>">

    <div></div>
</div>



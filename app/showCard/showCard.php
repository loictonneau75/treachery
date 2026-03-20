<?php
use App\Session\SessionTools;

require_once dirname(__DIR__,2) . "/session/tools.php";
?>

<div id="showCard">
    <div id="btnSortWrapper">
        <button class="active" type="button" id="btnSortByRole" data-value="role" data-action ="<?=BASE_URL?>app/showCard/cardAjax.php">Rôle</button>
        <button type="button" id="btnSortByRarity" data-value="rarity" data-action ="<?=BASE_URL?>app/showCard/cardAjax.php">Rareté</button>
    </div>

    <input type="hidden" name="csrfToken" value="<?=SessionTools::getData("csrf_token")?>">
</div>



<?php
use App\Session\SessionTools;
use App\CustomSelect\CustomSelect;

require_once dirname(__DIR__,2) . "/session/tools.php";
require_once dirname(__DIR__,2) . "/db/connexion.php";
require_once dirname(__DIR__) . "/customSelect/customSelect.php";
?>

<form action="<?=BASE_URL?>app/addCard/addCard.php" id="addCardForm">
    <h2>Ajouter un carte</h2>
    <div>
        <label class="label">Rôle :</label>
        <?=CustomSelect::renderCustomSelect($pdo, "role", "getRolesData")?>
    </div>
    <div>
        <label class="label">Rareté :</label>
        <?=CustomSelect::renderCustomSelect($pdo, "rarity", "getRarityData")?>
    </div>
    <div>
        <label for="card-img">Image de la carte :</label>
        <input type="file" id="card-img" name="img">
    </div>
    <div>
        <button type="submit">Enregister</button>
        <button type="button">Annuler</button>
    </div>
    <input type="hidden" name="csrf_token" value="<?=SessionTools::getData("csrf_token")?>">
    <input type="text" name="hp_email" style="display:none" autocomplete="off">
</form>
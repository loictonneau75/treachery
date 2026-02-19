<?php
use App\Session\SessionTools;
use App\CustomSelect\CustomSelect;

require_once dirname(__DIR__,3) . "/session/tools.php";
require_once dirname(__DIR__,3) . "/db/connexion.php";
require_once dirname(__DIR__,3) . "/db/tools.php";
require_once dirname(__DIR__,2) . "/customSelect.php";
?>

<form action="<?=BASE_URL?>app/partial/addCard/addCard.php" id="addCardForm">
    <h2>Ajouter un carte</h2>
    <?php
    CustomSelect::renderCustomSelect($pdo, "Rôle", "role", "getRolesData");
    CustomSelect::renderCustomSelect($pdo, "Rareté", "rarity", "getRarityData")
    ?>
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
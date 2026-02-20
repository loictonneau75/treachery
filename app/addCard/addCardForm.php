<?php
use App\Session\SessionTools;
use App\CustomSelect\CustomSelect;
use App\DB\DbTools;

require_once dirname(__DIR__,2) . "/session/tools.php";
require_once dirname(__DIR__,2) . "/db/connexion.php";
require_once dirname(__DIR__,2) . "/db/tools.php";
require_once dirname(__DIR__) . "/customSelect/customSelect.php";
?>

<form action="<?=BASE_URL?>app/addCard/addCard.php" id="addCardForm" enctype="multipart/form-data">
    <h2>Ajouter un carte</h2>
    <div>
        <label>Rôle :</label>
        <?=CustomSelect::renderCustomSelect($pdo, "role", DbTools::getAllFrom($pdo, 'roles'))?>
    </div>
    <div>
        <label>Rareté :</label>
        <?=CustomSelect::renderCustomSelect($pdo, "rarity", DbTools::getAllFrom($pdo, 'rarities'))?>
    </div>
    <div>
        <label for="cardImg">Image de la carte :</label>
        <input type="file" id="cardImg" name="cardImg">
    </div>
    <div>
        <button type="submit">Enregister</button>
        <button type="button">Annuler</button>
    </div>
    <input type="hidden" name="csrf_token" value="<?=SessionTools::getData("csrf_token")?>">
    <input type="text" name="hp_email" style="display:none" autocomplete="off">
</form>
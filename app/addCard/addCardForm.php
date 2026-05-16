<?php
use App\Session\SessionTools;
use App\CustomSelect\CustomSelect;
use App\CustomInputFile\CustomInputFile;
use App\DB\DbTools;

require_once dirname(__DIR__,2) . "/session/tools.php";
require_once dirname(__DIR__,2) . "/db/connexion.php";
require_once dirname(__DIR__,2) . "/db/tools.php";
require_once dirname(__DIR__) . "/customSelect/customSelect.php";
require_once dirname(__DIR__) . "/customInputFile/customInputFile.php";
?>

<form action="<?=BASE_URL?>app/addCard/addCard.php" id="addCardForm" enctype="multipart/form-data" class = "element">
    <h2>Ajouter un carte</h2>
    <div class="select-wrapper">
        <label>Rôle</label>
        <?php CustomSelect::renderCustomSelect("role")?>
    </div>
    <div class="select-wrapper">
        <label>Rareté</label>
        <?php CustomSelect::renderCustomSelect("rarity")?>
    </div>
    <div class="input-file-wrapper">
        <label>Image</label>
        <?php CustomInputFile::renderCustomInputFile("img")?>
    </div>
    <div>
        <button type="submit">Enregister</button>
        <button type="button">Annuler</button>
    </div>
    <input type="hidden" name="csrf_token" value="<?=SessionTools::getData("csrf_token")?>">
    <input type="text" name="hp_email" style="display:none" autocomplete="off">
</form>
<?php
$db = new DbTools($pdo);
CustomSelect::renderCustomSelectUl("role", $db -> getRoles());
CustomSelect::renderCustomSelectUl("rarity", $db -> getRarities())
?>
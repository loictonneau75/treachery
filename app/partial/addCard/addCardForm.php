<?php
use App\DB\DbTools;
use App\Session\SessionTools;

require_once dirname(__DIR__,3) . "/session/tools.php";
require_once dirname(__DIR__,3) . "/db/connexion.php";
require_once dirname(__DIR__,3) . "/db/tools.php";

?>

<form action="<?=BASE_URL?>app/partial/addCard/addCard.php" id="addCardForm">
    <h2>Ajouter un carte</h2>
    <?php
    renderCustomSelect($pdo, "Rôle", "role", "getTypesData");
    renderCustomSelect($pdo, "Rareté", "rarity", "getRarityData")
    ?>
    <div>
        <label for="card-img">Image de la carte :</label>
        <input type="file" id="card-img">
    </div>
    <div>
        <button type="submit">Enregister</button>
        <button type="button">Annuler</button>
    </div>
    <input type="hidden" name="csrf_token" value="<?=SessionTools::getData("csrf_token")?>">
    <input type="text" name="hp_email" style="display:none" autocomplete="off">
</form>




<?php
    function renderCustomSelect(PDO $pdo, string $label, string $inputName, string $dbFunction){
?>
        <div class="custom-select">
            <div>
                <label><?=$label?> :</label>
                <div>
                    <div></div>
                    <span>▼</span>
                </div>
            </div>
            <div>
                <div data-value = "">
                        <div class="caret"></div>
                        <span>-- Choisir --</span>
                </div>
                <?php foreach (DbTools::$dbFunction($pdo) as $data) : ?>
                    <div data-value="<?= (int)$data["id"] ?>">
                        <div class="caret"></div>
                        <img src="assets/img/<?=$inputName?>/<?= htmlspecialchars($data['url'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($data['name'], ENT_QUOTES) ?>">
                        <span><?= htmlspecialchars($data["name"]) ?></span>
                    </div>
                <?php endforeach ?>
            </div>
            <input type="hidden" name="addCard<?=ucfirst($inputName)?>">
        </div>
<?php
    }
?>

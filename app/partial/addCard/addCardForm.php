<?php
use App\DB\DbTools;
use App\Session\SessionTools;

require_once dirname(__DIR__,3) . "/session/tools.php";
require_once dirname(__DIR__,3) . "/db/connexion.php";
require_once dirname(__DIR__,3) . "/db/tools.php";

?>

<form action="<?=BASE_URL?>app/partial/addCard/addCard.php" id="addCardForm">
    <h2>Ajouter un carte</h2>
    <div>
        <label for="card-type">Type :</label>
        <select id="card-type">
            <option value="">-- Choisir --</option>
            <?php foreach (DbTools::getTypesData($pdo) as $data) :?>
                <option value="<?=(int)$data["id"]?>">
                    <img src="assets/img/roles/<?= htmlspecialchars($data['url'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($data['name'], ENT_QUOTES) ?>">
                    <?= htmlspecialchars($data["name"]) ?>
                </option>
            <?php endforeach ?>
        </select>
    </div>
    <div>
        <label for="card-rarity">Type :</label>
        <select id="card-rarity">
            <option value="">-- Choisir --</option>
            <?php foreach (DbTools::getRarityData($pdo) as $data) :?>
                <option value="<?=(int)$data["id"]?>">
                    <img src="assets/img/rarity/<?= htmlspecialchars($data['url'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($data['name'], ENT_QUOTES) ?>">
                    <?= htmlspecialchars($data["name"]) ?>
                </option>
            <?php endforeach ?>
            <option value="other">Autre</option>
        </select>
    </div>
    <div>
        <label for="card-img">Image de la carte :</label>
        <input type="file" id="card-img" name="card-img">
    </div>
    <div>
        <button type="submit">Enregister</button>
        <button type="button">Annuler</button>
    </div>
    <input type="hidden" name="csrf_token" value="<?=SessionTools::getData("csrf_token")?>">
    <input type="text" name="hp_email" style="display:none" autocomplete="off">
</form>
<?php
use App\DB\DbTools;

require_once dirname(__DIR__) . "/db/connexion.php";
require_once dirname(__DIR__) . "/db/tools.php";

include __DIR__ . "/navbar/navbar.php";
?>
<form>
    <h2>Rejoindre un salon</h2>
    <div>
        <input type="text" id="code">
        <label for="code">Code</label>
    </div>
    <button>Rejoindre</button>
</form>

<form>
    <h2>Creer un salon</h2>
    <div>
        <input type="number" id="nbPlayer" placeholder="" min=5>
        <label for="nbPlayer">nombre de joueur</label>
    </div>
    <button>Créer</button>
</form>

<form>
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
    <div class="file-input-group">
        <label for="card-img">Image de la carte :</label>
        <input type="file" id="card-img" name="card-img">
    </div>
    <div class="button-wrapper">
        <button type="submit">Enregister</button>
        <button type="button" id="removeAddCardButton">Annuler</button>
    </div>
    
</form>




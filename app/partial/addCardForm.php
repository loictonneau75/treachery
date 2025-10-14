<form id="formAddCard" action="<?= BASE_URL ?>app/card/addCard.php" >
    <div class="select-group">
        <label for="card-type">Type:</label>
        <select name="card-type" id="card-type">
            <option value="">-- Choisir --</option>
            <?php foreach (getTypesIdName($pdo) as $id => $data): ?>
                <option value="<?= (int)$id ?>">
                    <img src="assets/img/roles/<?= htmlspecialchars($data['url'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($data['name'], ENT_QUOTES) ?>", height="20px"/>
                    <?= htmlspecialchars($data["name"]) ?>
                </option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="select-group">
        <label for="card-rarity">Rarity:</label>
        <select name="card-rarity" id="card-rarity">
            <option value="">-- Choisir --</option>
            <?php foreach (getRaritiesIdName($pdo) as $id => $data): ?>
                <option value="<?= (int)$id ?>">
                    <img src="assets/img/rarity/<?= htmlspecialchars($data['url'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($data['name'], ENT_QUOTES) ?>", height="20px"/>
                    <?= htmlspecialchars($data["name"]) ?>
                </option>
            <?php endforeach ?>
            <option value="other">Autre</option>
        </select>
    </div>

    <div class="file-input-group">
        <label for="card-img">Image de la carte:</label>
        <input type="file" id="card-img" name="card-img">
    </div>
    <input type="text" name="website-a" id="website-a" autocomplete="off" style="display:none">

    <div class="button-wrapper">
        <button type="submit">Enregister</button>
        <button type="button" id="removeAddCardButton">Annuler</button>
    </div>
</form>
<button hidden type="button" class="show-toggle">Ajouter une carte</button>
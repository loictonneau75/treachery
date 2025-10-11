<form class="blur">
    <div class="select-group">
        <label for="card-type">Type:</label>
        <select name="card-type" id="card-type">
            <option value="">-- Choisir --</option>
            <?php foreach (getTypesIdName($pdo) as $id => $name): ?>
                <option value="<?= (int)$id ?>"><?= htmlspecialchars($name) ?></option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="select-group">
        <label for="card-rarity">Rarity:</label>
        <select name="card-rarity" id="card-rarity">
            <option value="">-- Choisir --</option>
            <?php foreach (getEnumValues($pdo, 'cards', 'rarity') as $rarity): ?>
                <option value="<?= htmlspecialchars($rarity, ENT_QUOTES) ?>"><?= htmlspecialchars($rarity) ?></option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="file-input-group">
        <label for="card-img">Image de la carte:</label>
        <input type="file" id="card-img" name="card-img">
    </div>
    <div class="button-wrapper">
        <button type="submit">Enregister</button>
        <button type="button">Annuler</button>
    </div>
</form>
<button type="button" hidden>Ajouter un nouvelle carte</button>
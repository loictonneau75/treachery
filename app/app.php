<?php
require_once __DIR__ ."/card/utils.php";
include __DIR__ . "/navbar/navbar.php";
?>

<div class="row">
    <?php
        include __DIR__ . "/partial/joinRoomForm.php";
        include __DIR__ . "/partial/createRoomForm.php";
    ?>
</div>
<div class="row alone">
        <form class="blur">
            <div class="select-group">
                <label for="card-type">Type de carte:</label>
                <select id="card-type" name="card-type">
                    <option value="">-- Choisir--</option>
                    <?php foreach (getRolesTypes($pdo) as $type): ?>
                        <option value="<?= htmlspecialchars($type)?>"><?= htmlspecialchars($type)?></option>
                    <?php endforeach;?>
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
</div>
<div class="row">


</div>
<div class="test"></div>
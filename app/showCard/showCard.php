<?php
use App\DB\DbTools;

require_once dirname(__DIR__,2) . "/config.php";
require_once dirname(__DIR__,2) . "/db/connexion.php";
require_once dirname(__DIR__,2) . "/db/tools.php";
?>
<form id="sortForm">
    <input type="radio" name="groupBy" id="role" value="role" checked>
    <label for="role">Rôle</label>
    <input type="radio" name="groupBy" id="rarity" value="rarity">
    <label for="rarity">Rareté</label>
</form>

<div class="cards-container">
    <?php foreach (DbTools::getCardsBy($pdo) as $card): ?>
        <!--todo retiré classe test -->
        <img class="test" src="<?=BASE_URL . "/assets/img/cards/" . $card['path']?>" alt="<?=$card['path']?>" data-role="<?=$card['role_id']?>" data-rarity="<?=$card['rarity_id']?>" data-id="<?=$card['id']?>">
    <?php endforeach; ?>
</div>


<?php
use App\Session\SessionTools;

require_once dirname(__DIR__, 2) . "/session/tools.php";
?>

<form>
    <h2>Creer un salon</h2>
    <div>
        <input type="number" id="nbPlayer" placeholder="" min=5>
        <label for="nbPlayer">nombre de joueur</label>
    </div>
    <input type="hidden" name="csrf_token" value="<?=SessionTools::getData("csrf_token")?>">
    <input type="text" name="hp_email" style="display:none" autocomplete="off">
    <button>Créer</button>
</form>
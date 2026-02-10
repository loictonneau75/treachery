<?php
use App\Session\SessionTools;

require_once dirname(__DIR__,2) . "/session/tools.php";

?>

<form action="<?=BASE_URL?>auth/register.php" id=formRegister hidden>
    <div class="input-wrapper">
        <input type="email" id="mailRegister" name="mailRegister" placeholder="" autocomplete="email">
        <label for="mailRegister">Entrez votre email</label>
    </div>
    <div class="input-wrapper">
        <input type="text" id="pseudoRegister" name="pseudoRegister" placeholder="" autocomplete="new-username">
        <label for="pseudoRegister">Entrez votre pseudo</label>
    </div>
    <div class="input-wrapper">
        <input type="password" id="passwordRegister" name="passwordRegister" placeholder="" autocomplete="new-password">
        <label for="passwordRegister">Entrez votre mot de passe</label>
    </div>
    <div class="input-wrapper">
        <input type="password" id="confirmRegister" name="confirmRegister" placeholder="" autocomplete="new-password">
        <label for="confirmRegister">Confirmez votre mot de passe</label>
    </div>
    <div>
        <input type="checkbox" id="rememberRegister" name="rememberRegister">
        <label for="rememberRegister" class="remember-toggle"></label>
        <label for="rememberRegister">Se souvenir de moi</label>
    </div>
    <input type="hidden" name="csrf_token" value="<?=SessionTools::getData("csrf_token")?>">
    <input type="text" name="hp_email" style="display:none" autocomplete="off">
    <button type="submit">S'inscrire</button>
</form>
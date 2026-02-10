<?php
use App\Session\SessionTools;

require_once dirname(__DIR__,2) . "/session/tools.php";

?>

<form action="<?=BASE_URL?>auth/login.php" id="formLogin">
    <div class="input-wrapper">
        <input type="email" id="mailLogin" name="mailLogin" placeholder="" autocomplete="email">
        <label for="mailLogin">Entrez votre email</label>
    </div>
    <div class="input-wrapper">
        <input type="password" id="passwordLogin" name="passwordLogin" placeholder="" autocomplete="new-password">
        <label for="passwordLogin">Entrez votre mot de passe</label>
    </div>
    <div>
        <input type="checkbox" id="rememberLogin" name="rememberLogin">
        <label for="rememberLogin" class="remember-toggle"></label>
        <label for="rememberLogin">Se souvenir de moi</label>
    </div>
    <input type="hidden" name="csrf_token" value="<?=SessionTools::getData("csrf_token")?>">
    <input type="text" name="hp_email" style="display:none" autocomplete="off">
    <button type="submit">Se connecter</button>
</form>
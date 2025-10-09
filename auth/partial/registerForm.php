<form method="post" id="formRegister" class="flex-column-center align-start" action="<?= BASE_URL ?>auth/register.php" hidden>
    <div class="input-group">
        <input type="email" id="mail-r" name="mail-r" placeholder="" autocomplete="email">
        <label for="mail-r">Entrez votre email</label>
    </div>
    <div class="input-group">
        <input type="text" id="pseudo" name="pseudo" placeholder="" autocomplete="new-username">
        <label for="pseudo">Entrez votre pseudo</label>
    </div>
    <div class="input-group">
        <input type="password" id="password-r" name="password-r" placeholder="" autocomplete="new-password">
        <label for="password">Entrez votre mot de passe</label>
    </div>
    <div class="input-group">
        <input type="password" id="confirm" name="confirm" placeholder="" autocomplete="new-password">
        <label for="confirm">Confirmez votre mot de passe</label>
    </div>
    <div>
        <input type="checkbox" id="remember-r" name="remember-r">
        <label for="remember-r" class="remember-toggle"></label>
        <label for="remember-r">Se souvenir de moi</label>
    </div>
    <input type="text" name="website-r" id="website-r" autocomplete="off" style="display:none">
    <button type="submit">S'inscrire</button>
</form>
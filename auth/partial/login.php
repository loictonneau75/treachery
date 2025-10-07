<form method="post" id="formLogin" class="flex-column-center align-start">
    <div class="input-group">
        <input type="email" id="mail" name="mail" placeholder="" autocomplete="email">
        <label for="mail">Entrez votre email</label>
    </div>
    <div class="input-group">
        <input type="password" id="password" name="password" placeholder="" autocomplete="new-password">
        <label for="password">Entrez votre mot de passe</label>
    </div>
    <div>
        <input type="checkbox" id="remember" name="remember">
        <label for="remember" class="remember-toggle"></label>
        <label for="remember">Se souvenir de moi</label>
    </div>
    <button type="submit" formaction="">Se connecter</button>
</form>
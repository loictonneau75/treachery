<div>
    <img src="assets/img/logo.png" alt="">
    <h1>Bienvenue sur <?=TITLE?></h1>
    <div>
        <?php
        include __DIR__."/partial/registerForm.php";
        include __DIR__."/partial/loginForm.php" ;

        ?>
        <div>
            <button class="active" type="button" id="btnShowLoginForm">Connexion</button>
            <button type="button" id="btnShowRegisterForm">Inscription</button>
        </div>
    </div>
</div>
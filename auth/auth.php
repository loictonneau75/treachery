<div class="flex-column-center">
    <img src="assets/img/logo.png" alt="logo" height="100px"/>
    <h1>Bienvenue sur <?=$title?></h1>
    <div class = "blur flex-column-center no-gap">
        <?php 
        include "partial/register.php";
        include "partial/login.php"
        ?>
        <div>
                <button type="button" id="btnLogin" class="active">Connexion</button>
                <button type="button" id="btnRegister">Inscription</button>
        </div>
    </div>
    
</div>

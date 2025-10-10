<?php
require_once dirname(__DIR__) . "/db/connexion.php";
require_once dirname(__DIR__) . "/db/utils.php";
?>


<nav class="blur">
    <div id="navbar">
        <img src="assets/img/logo_horizontal.png" alt="img/logo_horizontal.png">
        <div id="navbar-container">
            <h2><?=getPseudoById($pdo, $_SESSION["id"])?></h2>
            <button class="burger"><span></span><span></span><span></span></button>
        </div>
    </div>
    <div id="dropdown" hidden>
        <a href = "auth/logout.php">Se deconnecter</a>
        <a href = "">Suprimer son compte</a>
    </div>
</nav>
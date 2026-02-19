<?php
use App\Session\SessionTools;

require_once dirname(__DIR__) . "/session/tools.php";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=TITLE?></title>
    <link rel="icon" type="image/x-icon" href="assets/ico/favicon.ico">
    <link rel="stylesheet" href="css/style.css">
    <?php
    if(SessionTools::getData("id") === null) {
        echo "<script type='module' src='auth/partial/authForm.js'></script>";
        echo "<script type='module' src='auth/toggle.js'></script>";
    }
    else{
        echo "<script src='app/navbar/navbar.js' defer></script>";
        echo "<script type='module' src='app/customSelect/customSelect.js'></script>";
        echo "<script type='module' src='app/addCard/addCardForm.js'></script>";
        echo "<script type='module' src='app/createRoom/createRoomForm.js'></script>";
        echo "<script type='module' src='app/joinRoom/joinRoomForm.js'></script>";
    }

    ?>
</head>
<body class="<?=SessionTools::getData("id") ? 'app' : 'auth'?>">
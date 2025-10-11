<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= TITLE ?></title>
    <link rel="icon" type="image/x-icon" href="assets/ico/favicon.ico">
    <link rel="stylesheet" href="css/style.css">
    <?php
    if (!$loggedIn){
        echo "<script src='auth/auth.js' defer></script>";
    }else{
        echo"<script src='app/navbar/navbar.js' defer></script>";
        echo"<script src='app/app.js' defer></script>";
    }
    ?>
</head>
<body class="<?= $loggedIn ? 'app' : 'auth' ?>">

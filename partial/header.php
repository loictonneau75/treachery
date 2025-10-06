<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="icon" type="image/x-icon" href="assets/ico/favicon.ico">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="<?= $loggedIn ? 'app' : 'auth' ?>">
<?php
use App\Session\SessionTools;

require_once dirname(__DIR__) . "/session/tools.php";
require_once dirname(__DIR__) . "/config.php";

/** @var string $pageType */
/** @var string $pageName */

$pageType ??= '';
$pageName ??= '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$pageName?></title>
    <link rel="icon" type="image/x-icon" href=<?= BASE_URL .  "assets/ico/favicon.ico"?>>
    <link rel="stylesheet" href=<?=BASE_URL . "css/style.css"?>>
    <?php
    $scripts = [];
    if ($pageType === "index") {
        if (SessionTools::getData("id") === null) {
            $scripts[] = "auth/partial/authForm.js";
            $scripts[] = "auth/toggle.js";
        } else {
            $scripts[] = "navbar/navbar.js";
            $scripts[] = "app/customSelect/customSelect.js";
            $scripts[] = "app/customInputNumber/customInputNumber.js";
            $scripts[] = "app/customInputFile/customInputFile.js";
            $scripts[] = "app/addCard/addCardForm.js";
            $scripts[] = "app/createRoom/createRoomForm.js";
            $scripts[] = "app/joinRoom/joinRoomForm.js";
            $scripts[] = "app/showCard/showCard.js";
            $scripts[] = "app/preview/preview.js";
        }
    } elseif ($pageType === "room") {
        $scripts[] = "navbar/navbar.js";
        $scripts[] = "rooms/playersList/playerList.js";
        $scripts[] = "rooms/roleList/roleList.js";
        $scripts[] = "rooms/leaveRoom/leaveRoom.js";
    }

    foreach ($scripts as $script) {
        $script = BASE_URL . $script;
        echo "<script type='module' src='$script'></script>";
    }


    ?>
</head>
<?php

$bodyClass = match ($pageType) {
    "room" => "room",
    "index" => SessionTools::getData("id") === null ? "auth" : "app",
};
?>

<body class="<?= $bodyClass ?>">

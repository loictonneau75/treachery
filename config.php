<?php
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
define("TITLE", "Draw Your Fate");
define("BASE_URL", "$scheme://$host/treachery/");

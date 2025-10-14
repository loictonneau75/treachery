<?php
require_once dirname(__DIR__) . "/security/utils.php";

requirePostMethod();
honeyPot($_POST["website-a"]);
rateLimite("login");
throttle("login");
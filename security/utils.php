<?php
function honeyPot(?string $honeyPot): void{
    if (!empty($honeyPot)) {
        http_response_code(403);
        exit('Bot détecté');
    }
}

function rateLimite(string $action, int $limit = 5, int $windowSeconds = 60):void{
    $key = "rate_" . $action . "_" . $_SERVER["REMOTE_ADDR"];
    if(!isset($_SESSION[$key])){$_SESSION[$key] = ["count" => 0, "start" => time()];}
    if(time() - $_SESSION[$key]["start"] > $windowSeconds){
        $_SESSION[$key] = ["count" => 1, "start" => time()];
    }else{
        $_SESSION[$key]["count"]++;
        if($_SESSION[$key]["count"] > $limit){
            http_response_code(429);
            exit("Trop de requêtes, réessayez plus tard.");
        }
    }
}

function throttle(string $action, int $delaySeconds = 5): void {
    $key = 'last_' . $action . "_" . $_SERVER["REMOTE_ADDR"];
    if (isset($_SESSION[$key]) && (time() - $_SESSION[$key]) < $delaySeconds) {
        http_response_code(429);
        exit("Trop rapide, réessayez plus tard.");
    }
    $_SESSION[$key] = time();
}

function requirePostMethod(): void{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Méthode non autorisée');
    }
}
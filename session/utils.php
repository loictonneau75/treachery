<?php
function createSession(int $id, bool $remember): void{
    $_SESSION["id"] = $id;
    if($remember){
        setcookie("id", $id, time() + (30*24*60*60), "/");
    }
}

function resetSession(): void{
    if(!isset($_SESSION["id"]) && isset($_COOKIE["id"])){
        $_SESSION["id"] = $_COOKIE["id"];
    }
}

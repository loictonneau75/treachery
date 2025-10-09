<?php
function createSession(int $id, bool $remember): void{
    $_SESSION["id"] = $id;
    if($remember){
        setcookie("id", $id, time() + (30*24*60*60), "/");
    }
}
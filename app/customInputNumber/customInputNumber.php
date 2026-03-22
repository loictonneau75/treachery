<?php

namespace App\CustomInputNumber;

class CustomInputNumber{

    public static function renderCustomInputNumber($min, $max): void{
?>
        <div class="custom-input-number">
            <button type="button">+</button>
            <input id="nbPlayers" type="number" min=<?=$min?> max=<?=$max?> placeholder="">
            <button type="button">-</button>
        </div>
<?php
    }
}
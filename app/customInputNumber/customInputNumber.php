<?php

namespace App\CustomInputNumber;

class CustomInputNumber{

    public static function renderCustomInputNumber(): void{
?>
        <div class="custom-input-number">
            <button type="button">+</button>
            <input id="nbPlayer" type="number" min="5" max="8" required>
            <button type="button">-</button>
        </div>
<?php
    }
}
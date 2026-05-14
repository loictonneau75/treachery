<?php

namespace App\CustomInputFile;

class CustomInputFile{
//todo typer
    public static function renderCustomInputFile($inputName): void{
?>
        <div class="custom-input-file" id="card<?=ucfirst($inputName)?>Input">
            <label class="fake-button">Choisir un fichier
                <input type="file" id="card<?=ucfirst($inputName)?>" name="card<?=ucfirst($inputName)?>"/>
            </label>
            <span>Aucun fichier</span>
        </div>
<?php
    }
}
<?php

namespace App\CustomSelect;

use PDO;

class CustomSelect{

    public static function renderCustomSelect(PDO $pdo, string $inputName, array $dataList): void{
?>
        <div class="custom-select" id = "card<?=ucfirst($inputName)?>Input">
            <div class="fake-select">
                <span></span>
                <span>▼</span>
            </div>
            <ul>
                <li data-value="">
                    <div class="caret"></div>
                    <span>-- Choisir --</span>
                </li>

                <?php foreach ($dataList as $data): ?>
                    <li data-value="<?=(int)$data["id"]?>">
                        <div class="caret"></div>
                        <img src="assets/img/<?=$inputName?>/<?=htmlspecialchars($data['url'], ENT_QUOTES)?>" alt="<?=htmlspecialchars($data['name'], ENT_QUOTES)?>">
                        <span><?= htmlspecialchars($data["name"]) ?></span>
                    </li>
                <?php endforeach; ?>
                </ul>
            <input type="hidden" id="card<?=ucfirst($inputName)?>" name="card<?=ucfirst($inputName)?>">
        </div>
<?php
    }
}




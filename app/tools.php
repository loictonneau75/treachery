<?php

namespace App\Tools;

use PDO;
use App\DB\DbTools;

class AppTools{

    public static function renderCustomSelect(PDO $pdo, string $label, string $inputName, string $dbFunction): void{
?>
        <div class="custom-select">
            <label><?=htmlspecialchars($label)?> :</label>
            <div>
                <div></div>
                <span>▼</span>
            </div>
            <div>
                <div data-value="">
                    <div class="caret"></div>
                    <span>-- Choisir --</span>
                </div>

                <?php foreach (DbTools::$dbFunction($pdo) as $data): ?>
                    <div data-value="<?=(int)$data["id"]?>">
                        <div class="caret"></div>
                        <img src="assets/img/<?=$inputName?>/<?=htmlspecialchars($data['url'], ENT_QUOTES)?>" alt="<?=htmlspecialchars($data['name'], ENT_QUOTES)?>">
                        <span><?= htmlspecialchars($data["name"]) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <input type="hidden" id="card-<?=$inputName?>" name="<?=$inputName?>">
        </div>
<?php
    }
}


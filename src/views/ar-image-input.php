<?php

declare(strict_types=1);

use lakerLS\arImage\ArImageAsset;

/**
 * @var array $images
 * @var string $groupName
 * @var string $inputName
 * @var string $cartSize
 */

$bundle = ArImageAsset::register($this);
?>

<div class="ar-image">
    <div class="example-cart" style="display: none">
        <?= $this->renderFile(__DIR__ . '/_cart.php', [
            'preview' => true,
            'groupName' => $groupName,
            'src' => '{imgSrc}',
            'nameOld' => '{name}',
            'nameNew' => '{nameNew}',
            'alt' => '{alt}',
            'position' => '{imgPosition}',
            'cartSize' => $cartSize,
        ]) ?>
    </div>
    <div class="preview row">
        <?php
        foreach ($images as $image) {
            echo $this->renderFile(__DIR__ . '/_cart.php', [
                'preview' => false,
                'groupName' => $groupName,
                'src' => $image['src'],
                'nameOld' => $image['nameOld'] . '.' . $image['extension'],
                'nameNew' => $image['nameNew'],
                'alt' => $image['nameOld'],
                'position' => $image['position'],
                'cartSize' => $cartSize,
            ]);
        }
        ?>
    </div>
    <div class="management">
        <div>
            <div class="btn skin-file-input"><i class="icon-folder-open-empty"></i> <span>Выбрать изображения</span></div>
            <input type="file" name="<?= "{$groupName}[{$inputName}][]" ?>" accept="image/*" multiple>
        </div>
        <div>
            <div class="btn btn-danger delete-all-img"><i class="icon-cancel-circled2"></i> Удалить все изображения</div>
        </div>
        <div>
            <div class="btn small-cart <?= $cartSize == 'col-lg-1' ? 'active' : null ?>" title="Маленькие изображения"><i class="icon-th"></i></div>
            <div class="btn large-cart <?= $cartSize == 'col-lg-2' ? 'active' : null ?>" title="Крупные изображения"><i class="icon-th-large"></i></div>
        </div>
    </div>
</div>
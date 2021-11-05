<?php

declare(strict_types=1);

use lakerLS\arImage\ArImageAsset;

/**
 * @var bool $preview
 * @var string $groupName
 * @var string $cartSize
 * @var ArImageAsset $bundle
 */

?>

<div class="cart-size <?= $cartSize ?>">
    <div class="draggable <?= $preview ? 'new' : 'old' ?>">
        <input type="hidden" name="<?= "{$groupName}[arPosition][{nameNew}]" ?>" value="{imgPosition}">
        <div class="cart">
            <div class="img-container">
                <img data-modified="{modified}" src="<?= $bundle->baseUrl . '/image/placeholder.jpg' ?>" alt="{alt}">
            </div>
        </div>
    </div>
</div>
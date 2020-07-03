<?php

declare(strict_types=1);

/**
 * @var bool $preview
 * @var string $groupName
 * @var string $src
 * @var string $nameOld
 * @var string $nameNew
 * @var string $alt
 * @var integer $position
 * @var string $cartSize
 */

?>

<div class="cart-size <?= $cartSize ?>">
    <div class="draggable <?= $preview ? 'new' : 'old' ?>">
        <input type="hidden" name="<?= "{$groupName}[arPosition][{$nameNew}]" ?>" value="<?= $position ?>">
        <div class="cart">
            <div class="delete-img">
                <a href="#"><b>&#10060</b></a>
            </div>
            <div class="img-container">
                <img data-modified="{modified}" src="<?= $src ?>" alt="<?= $alt ?>">
            </div>
            <p><?= $nameOld ?></p>
        </div>
    </div>
</div>
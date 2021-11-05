<?php

declare(strict_types=1);

/**
 * @var bool $preview
 * @var string $groupName
 * @var string[] $image
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
            <div class="img-container">
                <a class="delete-img" href="#"><b>&#10060</b></a>
                <?= \lakerLS\arImage\widgets\ArImageThumbnail::widget([
                    'image' => $image,
                    'size' => ['width' => 264, 'height' => 373],
                    'quality' => 90,
                    'options' => [
                        'alt' => $nameNew,
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
</div>
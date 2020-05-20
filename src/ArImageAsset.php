<?php

declare(strict_types=1);

namespace lakerLS\arImage;

use yii\web\AssetBundle;

class ArImageAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/assets';
    public $publishOptions = ['forceCopy' => true];

    public $css = [
        'css/jquery-ui.css',
        'css/ar-image-fontello.css',
        'css/ar-image-input.css',
    ];
    public $js = [
        'js/jquery-ui.js',
        'js/ar-image-input.js',
    ];
}
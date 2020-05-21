<?php

declare(strict_types=1);

namespace lakerLS\arImage\widgets;

use lakerLS\arImage\components\ArImageCD;
use yii\base\Widget;
use yii\helpers\Html;

class ArImageThumbnail extends Widget
{
    /** @var string $src путь к оригинальному изображению. */
    public $src;

    /** @var array $size размеры миниатюры. */
    public $size;

    /** @var array Свойства тега img */
    public $options = [];

    /** @var string $imageFolderThumbnail путь к папке с миниатюрами изображений. */
    public $imageFolderThumbnail = 'ar_upload/thumbnail';

    /** @var string $imageNotFound путь к изображению, из которого будет создана миниатюра, если оригинал отсутствует. */
    public $imageNotFound = 'ar_upload/image-not-found.jpg';

    public function run()
    {
        parent::run();

        $arImageCD = new ArImageCD($this->imageFolderThumbnail, $this->imageNotFound);
        $srcThumbnail = $arImageCD->createThumbnail($this->src, $this->size);

        return Html::img($srcThumbnail, $this->options);
    }
}
<?php

declare(strict_types=1);

namespace lakerLS\arImage\widgets;

use Imagine\Image\ManipulatorInterface;
use lakerLS\arImage\components\ArImageCD;
use yii\base\Widget;
use yii\helpers\Html;

class ArImageThumbnail extends Widget
{
    /** @var array $image сериализованный массив с изображением. */
    public ?array $image;

    /** @var array $size размеры миниатюры. */
    public array $size;

    /** @var int $quality качество миниатюры. */
    public int $quality = 50;

    /** @var array $options Свойства тега img */
    public array $options = [];

    /** @var int $mode способ создания миниатюры, все варианты смотрите в данном интерфейсе. */
    public int $mode = ManipulatorInterface::THUMBNAIL_OUTBOUND;

    public function run()
    {
        parent::run();

        $arImageCD = new ArImageCD('ar_upload/thumbnail');
        if (!empty($this->image['src'])) {
            $srcThumbnail = $arImageCD->createThumbnail($this->image['src'], $this->size, $this->mode, $this->quality);
        } else {
            $srcThumbnail = $arImageCD->getImageNotFoundRelative();
        }

        return Html::img($srcThumbnail, $this->options);
    }
}
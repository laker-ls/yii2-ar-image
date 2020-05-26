<?php

declare(strict_types=1);

namespace lakerLS\arImage\widgets;

use Imagine\Image\ManipulatorInterface;
use lakerLS\arImage\components\ArImageCD;
use yii\base\Widget;
use yii\helpers\Html;

class ArImageThumbnail extends Widget
{
    /** @var string $images сериализованный массив с изображениями. */
    public $images;

    /** @var array $size размеры миниатюры. */
    public $size;

    /** @var array $options Свойства тега img */
    public $options = [];

    /** @var int $mode способ создания миниатюры, все варианты смотрите в данном интерфейсе. */
    public $mode = ManipulatorInterface::THUMBNAIL_OUTBOUND;

    /** @var string $imageFolderThumbnail путь к папке с миниатюрами изображений. */
    public $imageFolderThumbnail = 'ar_upload/thumbnail';

    /** @var string $imageNotFound путь к изображению, из которого будет создана миниатюра, если оригинал отсутствует. */
    public $imageNotFound = 'ar_upload/image-not-found.jpg';

    public function run()
    {
        parent::run();

        if (is_string($this->images)) {
            $images = unserialize($this->images);
        } else {
            $images[] = $this->images;
        }

        if ($images[0]['src']) {
            $arImageCD = new ArImageCD($this->imageFolderThumbnail, $this->imageNotFound);
            $srcThumbnail = $arImageCD->createThumbnail($images[0]['src'], $this->size, $this->mode);
        } else {
            $srcThumbnail = '/' . $this->imageNotFound;
        }

        return Html::img($srcThumbnail, $this->options);
    }
}
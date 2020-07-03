<?php

declare(strict_types=1);

namespace lakerLS\arImage\widgets;

use Imagine\Image\ManipulatorInterface;
use lakerLS\arImage\components\ArImageCD;
use yii\base\Widget;
use yii\helpers\Html;

class ArImageThumbnail extends Widget
{
    /** @var array $images сериализованный массив с изображениями. */
    public array $images;

    /** @var array $size размеры миниатюры. */
    public array $size;

    /** @var int $quality качество миниатюры. */
    public int $quality = 50;

    /** @var array $options Свойства тега img */
    public array $options = [];

    /** @var int $mode способ создания миниатюры, все варианты смотрите в данном интерфейсе. */
    public int $mode = ManipulatorInterface::THUMBNAIL_OUTBOUND;

    /** @var string $imageFolderThumbnail путь к папке с миниатюрами изображений. */
    public string $imageFolderThumbnail = 'ar_upload/thumbnail';

    /** @var string|null $imageNotFound путь к изображению в папке web, которое используется при отсутствии оригинала.
     * При значении 'null' используется изображение по умолчанию. */
    public ?string $imageNotFound = null;

    public function run()
    {
        parent::run();

        if ($this->images['src']) {
            $arImageCD = new ArImageCD($this->imageFolderThumbnail, $this->imageNotFound);
            $srcThumbnail = $arImageCD->createThumbnail($this->images['src'], $this->size, $this->mode, $this->quality);
        } else {
            $srcThumbnail = '/' . $this->imageNotFound;
        }

        return Html::img($srcThumbnail, $this->options);
    }
}
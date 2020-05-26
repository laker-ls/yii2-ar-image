<?php

declare(strict_types=1);

namespace lakerLS\arImage\components;

use yii\web\UploadedFile;
use yii\imagine\Image;
use Yii;

/**
 * Работа с файлами изображений.
 * Class ArImageCD
 * @package lakerLS\arImage\components
 */
class ArImageCD
{
    /** @var string $imageFolder путь к оригинальному изображению. */
    private $imageFolder;

    /** @var string $imageNotFound путь к изображению, которе используется при отсутствии оригинала. */
    private $imageNotFound;

    /** @var string $imageName наименование загружаемого изображения.  */
    private $nameImage;

    public function __construct(string $imageFolder, string $imageNotFound)
    {
        $this->imageFolder = $imageFolder;
        $this->imageNotFound = $imageNotFound;
    }

    /**
     * Загрузка изображения на сервер.
     * @param UploadedFile $image
     * @return string
     */
    public function uploadOriginal(UploadedFile $image) :string
    {
        $this->nameImage = $this->generateNameImage($image->baseName, $image->extension);
        $path = $this->generateFolder($this->imageFolder);

        if ($image->saveAs($path['full'])) {
            return $path['relative'];
        } else {
            return '/' . $this->imageNotFound;
        }
    }

    /**
     * Создание миниатюры изображения.
     * @param string $srcOriginal
     * @param array $size
     * @param int $mode
     * @return string
     */
    public function createThumbnail(string $srcOriginal, array $size, int $mode) : string
    {
        $piecesPath = explode('/', $srcOriginal);
        $nameImage = array_pop($piecesPath);
        $date = array_pop($piecesPath);
        $this->nameImage = $nameImage;
        $fullPathOriginal = Yii::getAlias('@webroot' . $srcOriginal);

        $sizeAsString = implode('x', $size);
        $pathThumbnail = $this->generateFolder($this->imageFolder, $sizeAsString, $date);

        if (!file_exists($fullPathOriginal)) {
            $fullPathOriginal = Yii::getAlias('@webroot/' . $this->imageNotFound);
        }

        if (!file_exists($pathThumbnail['full'])) {
            $thumbnail = Image::thumbnail($fullPathOriginal, $size['width'], $size['height'], $mode);
            if ($thumbnail->save($pathThumbnail['full'])) {
                return $pathThumbnail['relative'];
            } else {
                return '/' . $this->imageNotFound;
            }
        } else {
            return $pathThumbnail['relative'];
        }
    }

    /**
     * Удаление изображений физически, а так же данных о нем в переменной $oldImages.
     * @param array $imageForDelete
     * @return bool
     */
    public function deleteImage(array $imageForDelete) : bool
    {
        $fullPathOriginal = Yii::getAlias('@webroot') . $imageForDelete['src'];
        if (file_exists($fullPathOriginal)) {
            unlink($fullPathOriginal);
        }

        $fullPathThumbnail = str_replace('/original/', '/thumbnail/', $fullPathOriginal);
        $fullPathThumbnail = explode('/', $fullPathThumbnail);
        $nameThumbnail = array_pop($fullPathThumbnail);
        $fullPathThumbnail = implode('/', $fullPathThumbnail);

        if (file_exists($fullPathThumbnail)) {
            $foldersSize = scandir($fullPathThumbnail);
            unset($foldersSize[0], $foldersSize[1]);
            foreach($foldersSize as $folderSize) {
                $images = scandir($fullPathThumbnail . '/' . $folderSize);
                unset($images[0], $images[1]);
                foreach ($images as $name) {
                    $thumbnailImage = $fullPathThumbnail . '/' . $folderSize . '/' . $name;
                    if (file_exists($thumbnailImage) && $name == $nameThumbnail) {
                        unlink($thumbnailImage);
                    }
                }
            }
        }

        return true;
    }

    /**
     * Создание нового, уникального имени для изображения.
     * @param string $name
     * @param string $extenstion
     * @return string
     */
    private function generateNameImage(string $name, string $extenstion) : string
    {
        $randomNumber = time() + rand();
        $newName = md5($name . $randomNumber);

        return "{$newName}.{$extenstion}";
    }

    /**
     * Создание папок и формирование полного пути.
     * @param string $imageFolder
     * @param string|null $size
     * @param string|null $date
     * @return array
     */
    private function generateFolder(string $imageFolder, string $size = null, string $date = null) :array
    {
        $rootPath = Yii::getAlias('@webroot/') . $imageFolder;
        $date = $date ?? date('y-m-d');
        $size .= $size ? '/' : null;

        $fullPath = $rootPath . '/' . $date . '/' . $size;
        $relativePath = '/' . $imageFolder . '/' . $date . '/' . $size;

        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0777, true);
        }

        return [
            'full' => $fullPath . $this->nameImage,
            'relative' => $relativePath . $this->nameImage,
        ];
    }
}
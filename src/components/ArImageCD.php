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
    private string $imageFolder;

    /** @var string $imageNotFoundRelative путь к изображению, которе используется при отсутствии оригинала. */
    private string $imageNotFoundRelative;

    /** @var string $imageNotFoundFull путь к изображению от корня, которе используется при отсутствии оригинала. */
    private string $imageNotFoundFull;

    /** @var string $imageName наименование загружаемого изображения.  */
    private string $nameImage;

    public function __construct(string $imageFolder)
    {
        $this->imageNotFoundRelative = '/ar_upload/image-not-found.jpg';
        $this->imageNotFoundFull = Yii::getAlias('@webroot') . $this->imageNotFoundRelative;
        $this->imageFolder = $imageFolder;
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

        if (!$image->saveAs($path['full'])) {
            copy($this->imageNotFoundFull, $path['full']);
        }

        return $path['relative'];
    }

    /**
     * Создание миниатюры изображения.
     * @param string $srcOriginal
     * @param array $size
     * @param int $mode
     * @param int $quality
     * @return string
     */
    public function createThumbnail(string $srcOriginal, array $size, int $mode, int  $quality) : string
    {
        $piecesPath = explode('/', $srcOriginal);
        $nameImage = array_pop($piecesPath);
        $date = array_pop($piecesPath);
        $this->nameImage = $nameImage;
        $fullPathOriginal = Yii::getAlias('@webroot' . $srcOriginal);

        $sizeAsString = implode('x', $size);
        $pathThumbnail = $this->generateFolder($this->imageFolder, $sizeAsString, $date);

        if (!file_exists($fullPathOriginal)) {
            $fullPathOriginal = $this->imageNotFoundFull;
        }

        if (!file_exists($pathThumbnail['full'])) {
            $thumbnail = Image::thumbnail($fullPathOriginal, $size['width'], $size['height'], $mode);
            if ($thumbnail->save($pathThumbnail['full'], ['quality' => $quality])) {
                return $pathThumbnail['relative'];
            } else {
                return '/' . $this->imageNotFoundRelative;
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
     * Получить относительный путь к изображению "изображение не найдено".
     * @return string
     */
    public function getImageNotFoundRelative() : string
    {
        return $this->imageNotFoundRelative;
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
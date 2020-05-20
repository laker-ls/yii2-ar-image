<?php

declare(strict_types=1);

namespace lakerLS\arImage\behaviors;

use lakerLS\arImage\helpers\MainHelper;
use yii\base\Behavior;
use yii\base\Event;
use yii\base\ModelEvent;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\imagine\Image;
use yii\web\UploadedFile;
use Yii;

class ArImageBehaviors extends Behavior
{
    /**
     * @var array $fields обязательный параметр со списком полей, в которых необходимо обрабатывать изображения.
     * Пример: ['image', 'text']
     */
    public $fields;

    /** @var string $imageFolderOriginal путь к оригинальному изображению. */
    public $imageFolderOriginal = 'ar_upload/original';

    /** @var string $imageFolderThumbnail путь к миниатюре изображения. */
    public $imageFolderThumbnail = 'ar_upload/thumbnail';

    /** @var string $imageNotFound изображение, используемое при отсутствии оригинала. */
    public $imageNotFound = 'ar_upload/image-not-found.jpg';

    /** @var string $imageName наименование загружаемого изображения.  */
    private $nameImage;

    /**
     * {@inheritdoc}
     */
    public function events() :array
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'eventInsertUpdate',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'eventInsertUpdate',
            ActiveRecord::EVENT_AFTER_DELETE => 'eventDelete',
        ];
    }

    /**
     * Событие создания и обновления записи.
     * @param ModelEvent $event
     * @return void
     */
    public function eventInsertUpdate(ModelEvent $event) :void
    {
        $modelName = MainHelper::dynamicClass($event->sender);
        $post = Yii::$app->request->post($modelName);
        $positions = $post['position'];

        foreach ($this->fields as $field) {
            $oldData = !empty($event->sender->$field) ? unserialize($event->sender->$field) : [];
            $thumbnailsWidth = (int)$post["{$field}Options"]['thumbnailsWidth'];
            $thumbnailsHeight = (int)$post["{$field}Options"]['thumbnailsHeight'];

            $images = UploadedFile::getInstancesByName("{$modelName}[{$field}]");
            $newData = [];
            foreach ($images as $key => $image) {
                $srcOriginal = $this->uploadOriginal($image);
                $srcThumbnail = $this->createThumbnail($image, $thumbnailsWidth, $thumbnailsHeight);
                $newData[] = $this->prepareData($image, $srcOriginal, $srcThumbnail);
            }

            $this->deleteImages($oldData, $post['position']);

            $result = ArrayHelper::merge($oldData, $newData);
            $result = $this->sortByPosition($result, $positions);
            $result = serialize($result);

            $event->sender->$field = $result;
        }
    }

    /**
     * Событие удаления записи.
     * @param Event $event
     * @return void
     */
    public function eventDelete(Event $event) :void
    {
        foreach ($this->fields as $field) {
            $oldData = !empty($event->sender->$field) ? unserialize($event->sender->$field) : [];

            $this->deleteImages($oldData,);
        }
    }

    /**
     * Загрузка изображения на сервер.
     * @param UploadedFile $image
     * @return string
     */
    private function uploadOriginal(UploadedFile $image) :string
    {
        $this->nameImage = $this->generateNameImage($image->baseName, $image->extension);
        $path = $this->generateFolder($this->imageFolderOriginal);

        if ($image->saveAs($path['full'], false)) {
            return $path['relative'];
        } else {
            return '/' . $this->imageNotFound;
        }
    }

    /**
     * Создание миниатюры изображения.
     * @param UploadedFile $image
     * @param int $width
     * @param int $height
     * @return string
     */
    private function createThumbnail(UploadedFile $image, int $width, int $height) : string
    {
        if (empty($this->nameImage)) {
            $this->nameImage = $this->generateNameImage($image->baseName, $image->extension);
        }
        $path = $this->generateFolder($this->imageFolderThumbnail);

        $thumbnail = Image::thumbnail($image->tempName, $width, $height);
        if ($thumbnail->save($path['full'])) {
            return $path['relative'];
        } else {
            return 'error';
        }
    }

    /**
     * Удаление изображений физически, а так же данных о нем в переменной $oldImages.
     * @param array $oldImages
     * @param array $actualImages
     * @return bool
     * @throws \Exception
     */
    private function deleteImages(array &$oldImages, array $actualImages = null) : bool
    {
        if ($actualImages) {
            $needDelete = $this->selectImagesForDelete($oldImages, $actualImages);
        } else {
            $needDelete = $oldImages;
        }

        foreach ($needDelete as $key => $image) {
            $fullPathOriginal = Yii::getAlias('@webroot') . $image['srcOriginal'];
            $fullPathThumbnail = Yii::getAlias('@webroot') . $image['srcThumbnail'];

            if (file_exists($fullPathOriginal)) {
                unlink($fullPathOriginal);
            }
            if (file_exists($fullPathThumbnail)) {
                unlink($fullPathThumbnail);
            }

            unset($oldImages[$key]);
        }

        return true;
    }

    /**
     * Сравнение существующих имен изображений с полученными, для определения изображений на удаление.
     * @param array $oldImages
     * @param array $actualImages
     * @return array
     */
    private function selectImagesForDelete(array $oldImages, array $actualImages) : array
    {
        $needDelete = [];
        foreach ($oldImages as $key => $oldImageData) {
            foreach ($actualImages as $nameActualImage => $position) {
                if ($nameActualImage != $oldImageData['nameNew']) {
                    $needDelete[$key] = $oldImageData;
                } else {
                    unset($needDelete[$key]);
                    break;
                }
            }
        }

        return $needDelete;
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
     * @return array
     */
    private function generateFolder(string $imageFolder) :array
    {
        $rootPath = Yii::getAlias('@webroot/') . $imageFolder;
        $data = date('y-m-d');
        $fullPath = "{$rootPath}/{$data}/";
        $relativePath = "/{$imageFolder}/{$data}/";

        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0777, true);
        }

        return [
            'full' => $fullPath . $this->nameImage,
            'relative' => $relativePath . $this->nameImage,
        ];
    }

    /**
     * Подготовка данных для записи в таблицу.
     * @param UploadedFile $image
     * @param string $srcOriginal
     * @param string $srcThumbnail
     * @return array
     */
    private function prepareData(UploadedFile $image, string $srcOriginal, string $srcThumbnail) : array
    {
        $nameOld = explode('.', $image->name);
        array_pop($nameOld);
        $nameOld = implode('', $nameOld);

        $nameNew = explode('/', $srcOriginal);
        $nameNew = array_pop($nameNew);
        $nameNew = explode('.', $nameNew);
        array_pop($nameNew);
        $nameNew = implode('', $nameNew);

        $data['srcOriginal'] = $srcOriginal;
        $data['srcThumbnail'] = $srcThumbnail;
        $data['nameOld'] = $nameOld;
        $data['nameNew'] = $nameNew;
        $data['extension'] = $image->extension;
        $data['size'] = $image->size;

        return $data;
    }

    /**
     * Сортировка данных по позиций.
     * @param array $data
     * @param array $positions
     * @return array
     */
    private function sortByPosition(array $data, array $positions) : array
    {
        $quantityImages = count($data);
        for ($i = 0; $i < $quantityImages; $i++) {
            foreach ($positions as $name => $newKey) {
                if ($data[$i]["nameNew"] == $name || ($data[$i]["nameNew"] != $name && $data[$i]["nameOld"] == $name)) {
                    $data[$i]['position'] = $newKey;
                }
            }
        }
        uasort($data, function ($a, $b) {
            return ($a['position'] > $b['position']);
        });
        $data = array_values($data);

        return $data;
    }
}

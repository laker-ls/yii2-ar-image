<?php

declare(strict_types=1);

namespace lakerLS\arImage\behaviors;

use lakerLS\arImage\components\ArImageCD;
use lakerLS\arImage\helpers\MainHelper;
use yii\base\Behavior;
use yii\base\Event;
use yii\base\ModelEvent;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use Yii;

class ArImageBehavior extends Behavior
{
    /**
     * @var array $fields обязательный параметр со списком полей, в которых необходимо обрабатывать изображения.
     * Пример: ['image', 'text']
     */
    public $fields;

    /** @var string $imageFolderOriginal путь к папке, в которой хранятся оригинальные изображения. */
    public $imageFolderOriginal = 'ar_upload/original';

    /** @var string $imageNotFound путь к изображению, которое используется при отсутствии оригинала. */
    public $imageNotFound = 'ar_upload/image-not-found.jpg';

    /** @var ArImageCD $arImage объект для работы с физическими изображениями. */
    private $arImage;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->arImage = new ArImageCD($this->imageFolderOriginal, $this->imageNotFound);
    }

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
        $positions = $post['arPosition'];

        foreach ($this->fields as $field) {
            $oldData = $event->sender->$field;
            if (is_array($oldData)) {
                throw new \Exception('Form must have a property "multipart/form-data".');
            }
            $oldData = !empty($event->sender->$field) ? unserialize($event->sender->$field) : [];
            $oldData = $oldData ? $oldData : [];

            $images = UploadedFile::getInstancesByName("{$modelName}[{$field}]");
            $newData = [];
            foreach ($images as $key => $image) {
                $srcOriginal = $this->arImage->uploadOriginal($image);
                $newData[] = $this->prepareData($image, $srcOriginal);
            }

            $imagesForDelete = $this->selectImagesForDelete($oldData, $post['arPosition']);
            foreach ($imagesForDelete as $imageDelete) {
                $this->arImage->deleteImage($imageDelete);
            }

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
            $imagesForDelete = !empty($event->sender->$field) ? unserialize($event->sender->$field) : [];
            foreach ($imagesForDelete as $imageDelete) {
                $this->arImage->deleteImage($imageDelete);
            }
        }
    }

    /**
     * Сравнение существующих имен изображений с полученными, для определения изображений на удаление.
     * @param array $oldImages
     * @param array|null $actualImages
     * @return array
     */
    private function selectImagesForDelete(array &$oldImages, ?array $actualImages) : array
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

        foreach ($needDelete as $key => $data) {
            unset($oldImages[$key]);
        }

        return $needDelete;
    }

    /**
     * Подготовка данных для записи в таблицу.
     * @param UploadedFile $image
     * @param string $srcOriginal
     * @return array
     */
    private function prepareData(UploadedFile $image, string $srcOriginal) : array
    {
        $nameOld = explode('.', $image->name);
        array_pop($nameOld);
        $nameOld = implode('', $nameOld);

        $nameNew = explode('/', $srcOriginal);
        $nameNew = array_pop($nameNew);
        $nameNew = explode('.', $nameNew);
        array_pop($nameNew);
        $nameNew = implode('', $nameNew);

        $data['src'] = $srcOriginal;
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

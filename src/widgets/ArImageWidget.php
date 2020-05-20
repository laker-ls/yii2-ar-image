<?php

namespace lakerLS\arImage\widgets;

use lakerLS\arImage\helpers\MainHelper;
use yii\widgets\InputWidget;

class ArImageWidget extends InputWidget
{
    /**
     * @var array $thumbnails параметры для миниатюр.
     * (integer) width - ширина миниатюры в пикселях.
     * (integer) height - высота миниатюры в пикселях.
     * ПРИМЕР:
     * ['thumbnails' => ['width' => 200, 'height' => 100]]
     */
    public $thumbnails;

    /**
     * @var string $cartSize размер карточек по умолчанию.
     * Варианты: normal|large
     */
    public $cartSize = 'normal';

    public function run()
    {
        parent::run();

        $images = $this->model->attributes[$this->attribute];
        $images = $images ? unserialize($images) : [];
        $modelName = MainHelper::dynamicClass($this->model);

        return $this->render('../../views/ar-image-input', [
            'thumbnailsWidth' => $this->thumbnails['width'],
            'thumbnailsHeight' => $this->thumbnails['height'],
            'images' => $images,
            'groupName' => $modelName,
            'inputName' => $this->attribute,
            'cartSize' => $this->getCartSize(),
        ]);
    }

    private function getCartSize()
    {
        switch ($this->cartSize) {
            case 'normal':
                return 'col-lg-1';
            case 'large':
                return 'col-lg-2';
            default:
                throw new Exception('Property "cartSize" have not correct value.');
        }
    }
}

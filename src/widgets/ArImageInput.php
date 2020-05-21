<?php

declare(strict_types=1);

namespace lakerLS\arImage\widgets;

use lakerLS\arImage\helpers\MainHelper;
use yii\widgets\InputWidget;

class ArImageInput extends InputWidget
{
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

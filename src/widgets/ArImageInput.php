<?php

declare(strict_types=1);

namespace lakerLS\arImage\widgets;

use lakerLS\arImage\helpers\MainHelper;
use yii\widgets\InputWidget;

class ArImageInput extends InputWidget
{
    /**
     * @var string $cartSize размер карточек по умолчанию.
     * Варианты: extra-small|small|normal|large|extra-large
     */
    public string $cartSize = 'normal';

    public function run()
    {
        parent::run();

        $images = $this->model->attributes[$this->attribute];
        $images = $images ? unserialize($images) : [];
        $images = $images ? $images : [];
        $modelName = MainHelper::dynamicClass($this->model);

        return $this->render('../../views/ar-image-input', [
            'images' => $images,
            'groupName' => $modelName,
            'inputName' => $this->attribute,
            'cartSize' => $this->getCartSize(),
        ]);
    }

    private function getCartSize() : string
    {
        switch ($this->cartSize) {
            case 'extra-small':
                return 'table';
            case 'small':
                return 'col-lg-1';
            case 'normal':
                return 'col-lg-2';
            case 'large':
                return 'col-lg-4';
            case 'extra-large':
                return 'col-lg-6';
            default:
                throw new \Exception('Property "cartSize" have not correct value.');
        }
    }
}

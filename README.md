<h1 align="center">
    yii2-ar-image
</h1>


[![Stable Version](https://poser.pugx.org/laker-ls/yii2-ar-image/v/stable)](https://packagist.org/packages/laker-ls/yii2-nested-set-menu)
[![Unstable Version](https://poser.pugx.org/laker-ls/yii2-ar-image/v/unstable)](https://packagist.org/packages/laker-ls/yii2-nested-set-menu)
[![License](https://poser.pugx.org/laker-ls/yii2-ar-image/license)](https://packagist.org/packages/laker-ls/yii2-nested-set-menu)
[![Total Downloads](https://poser.pugx.org/laker-ls/yii2-ar-image/downloads)](https://packagist.org/packages/laker-ls/yii2-nested-set-menu)

Это расширение предназначено для загрузки изображений на yii2 и содержит в себе WidgetInput и Behavior.

> ВАЖНО: Расширение находится в активной разработке, без совместимости с предыдущими мажорными релизами.

## Установка

Рекомендуемый способ установки этого расширения является использование [composer](http://getcomposer.org/download/).
Проверьте [composer.json](https://github.com/laker-ls/yii2-nested-set-menu/blob/master/composer.json) на предмет требований и зависимостей данного расширения.

Для установки запустите

```
$ php composer.phar require laker-ls/yii2-ar-image "~0.1.0"
```

или добавьте в `composer.json` в раздел `require` следующую строку

```
"laker-ls/yii2-ar-image": "~0.1.0"
```

> Смотрите [список изменений](https://github.com/laker-ls/yii2-ar-image/blob/master/CHANGE.md) для подробной информации о версиях.

## Использование

Использования виджета в ActiveRecord:
```php
use lakerLS\arImage\widgets\ArImageWidget;

echo $form->field($model, 'image')->widget(ArImageWidget::class, [
    'thumbnails' => [
        'width' => 300,
        'height' => 200,
    ]
]);
```

`thumbnails` - обязательное свойство, в котором указываются размеры миниатюры. <br />
`cartSize` - размер карточек изображений. По умолчанию 'normal'. Возможные значения: normal, large. <br />

Подключение поведения в необходимой модели:
```php
use lakerLS\arImage\behaviors\ArImageBehaviors;

public function behaviors()
{
    return [
        'ArImageBehaviors' => [
            'class' => ArImageBehaviors::class,
            'fields' => ['image'],
        ]
    ];
}
```

`fields` - обязательное свойство, в котором указываются поля таблицы, в которых будут сохраняться изображения. <br />
`imageFolderOriginal` - расположение оригинальных изображений в папке `web`. <br />
`imageFolderThumbnail` - расположение миниатюр изображений в папке `web`. <br />
`imageNotFound` - расположение изображения в папке `web`, которое используется при отсутствии необходимого изображения. <br />

## Лицензия

**yii2-nested-set-menu** выпущено по лицензии BSD-3-Clause. Ознакомиться можно в файле `LICENSE.md`.

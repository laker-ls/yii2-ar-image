<h1 align="center">
    yii2-ar-image
</h1>

[![Stable Version](https://poser.pugx.org/laker-ls/yii2-ar-image/v/stable)](https://packagist.org/packages/laker-ls/yii2-ar-image)
[![Unstable Version](https://poser.pugx.org/laker-ls/yii2-ar-image/v/unstable)](https://packagist.org/packages/laker-ls/yii2-ar-image)
[![License](https://poser.pugx.org/laker-ls/yii2-ar-image/license)](https://packagist.org/packages/laker-ls/yii2-ar-image)
[![Total Downloads](https://poser.pugx.org/laker-ls/yii2-ar-image/downloads)](https://packagist.org/packages/laker-ls/yii2-ar-image)

Это расширение предназначено для загрузки изображений на yii2 и содержит в себе WidgetInput и Behavior.

> ВАЖНО: Расширение находится в активной разработке, без совместимости с предыдущими мажорными релизами.

## Установка

Рекомендуемый способ установки этого расширения является использование [composer](http://getcomposer.org/download/).
Проверьте [composer.json](https://github.com/laker-ls/yii2-nested-set-menu/blob/master/composer.json) на предмет требований и зависимостей данного расширения.

Для установки запустите

```
$ php composer.phar require laker-ls/yii2-ar-image "~0.6.2"
```

или добавьте в `composer.json` в раздел `require` следующую строку

```
"laker-ls/yii2-ar-image": "~0.6.2"
```

> Смотрите [список изменений](https://github.com/laker-ls/yii2-ar-image/blob/master/CHANGE.md) для подробной информации о версиях.

## Использование

Использования виджета в ActiveRecord:
```php
use lakerLS\arImage\widgets\ArImageInput;

echo $form->field($model, 'image')->widget(ArImageInput::class);
```

`cartSize` - размер карточек изображений. По умолчанию 'normal'. Возможные значения: normal, large. <br />

Подключение поведения в необходимой модели:
```php
use lakerLS\arImage\behaviors\ArImageBehavior;

public function behaviors()
{
    return [
        'ArImageBehavior' => [
            'class' => ArImageBehavior::class,
            'fields' => ['image'],
        ]
    ];
}
```

`fields` - обязательное свойство, в котором указываются поля таблицы, в которых будут сохраняться изображения. <br />
`imageFolderOriginal` - путь к папке, в которой хранятся оригинальные изображения. <br />
`imageNotFound` - путь к изображению, которое используется при отсутствии оригинала. <br />

Использование виджета для отображения изображений на странице. Отображение одного изображения.
```php
use lakerLS\arImage\widgets\ArImageThumbnail; 

$result = ArImageThumbnail::widget([
    'image' => unserialize($model->images)[0],
    'size' => ['width' => 300, 'height' => 200],
    'options' => [
        'alt' => 'Наименование изображения',
        'data-examplt' => 'Необходимые атрибуты'
    ],
]);
```

Использование виджета для отображения изображений на странице. Отображение нескольких изображений.
```php
use lakerLS\arImage\widgets\ArImageThumbnail; 

foreach (unserialize($model->images) as $image) {
    $result = ArImageThumbnail::widget([
        'image' => $image,
        'size' => ['width' => 300, 'height' => 200],
        'options' => [
            'alt' => 'Наименование изображения',
            'data-examplt' => 'Необходимые атрибуты'
        ],
    ]);
}
```

`src` - путь, по которому хранится оригинальное изображение.
`size` - размеры миниатюры.
`options` - свойства тега img.
`imageFolderThumbnail` - путь к папке, в которой хранятся миниатюры изображения. <br />
`imageNotFound` - путь к изображению, из которого будет создана миниатюра, если оригинал отсутствует. <br />

## Лицензия

**yii2-nested-set-menu** выпущено по лицензии BSD-3-Clause. Ознакомиться можно в файле `LICENSE.md`.

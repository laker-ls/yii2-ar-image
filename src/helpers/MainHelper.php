<?php

declare(strict_types=1);

namespace lakerLS\arImage\helpers;

class MainHelper
{
    /**
     * Получить наименование динамического класса.
     * @param $object
     * @return string
     */
    public static function dynamicClass(object $object) :string
    {
        $objectName = get_class($object);
        $objectName = explode('\\', $objectName);
        $objectName = array_pop($objectName);
        
        return $objectName;
    }
}
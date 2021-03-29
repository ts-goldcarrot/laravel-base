<?php

namespace GoldcarrotLaravel\Base\Enums;


use GoldcarrotLaravel\Base\Interfaces\EnumsInterface;

abstract class BaseEnums implements EnumsInterface
{
    abstract public static function keys(): array;

    public static function labels(): array
    {
        return array_combine(static::all(), static::all());
    }
}

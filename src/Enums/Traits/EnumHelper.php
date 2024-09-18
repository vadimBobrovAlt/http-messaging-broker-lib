<?php

namespace bobrovva\http_messaging_broker_lib\Enums\Traits;

trait EnumHelper
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function array(): array
    {
        $array = [];
        foreach (self::cases() as $item) {
            $array[$item->name] = $item->value;
        }

        return $array;
    }

    public static function findByName(string $name, string $return = 'value')
    {
        foreach (self::cases() as $item) {
            if ($name == $item->name) {
                switch ($return) {
                    case  'class':
                        return $item;
                    case  'value':
                        return $item->value;
                }
            }
        }

        return null;
    }

    public static function getEnumByData($enum)
    {
        if (is_int($enum)) {
            $enum = self::from($enum);
        } elseif (is_string($enum)) {
            $enum = strtoupper($enum);
            $enum = self::findByName($enum, 'class');
        }

        return $enum;
    }
}

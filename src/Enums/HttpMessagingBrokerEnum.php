<?php

namespace App\Infrastructure\HttpMessagingBroker\Enums;


use bobrovva\http_messaging_broker_lib\Enums\ServiceEnum;
use bobrovva\http_messaging_broker_lib\Enums\Traits\EnumHelper;

/**
 * Перечисление HttpMessagingBrokerEnum
 *
 * Это перечисление содержит ключевые действия для взаимодействия с брокером сообщений через HTTP.
 */
enum HttpMessagingBrokerEnum: string
{
    use EnumHelper;

    /**
     * Действие для создания сущности в сервисе.
     */
    case BASE_CREATE = 'base_create';

    /**
     * Получает сервис, связанный с указанным действием брокера сообщений.
     *
     * @param self $enum Действие брокера сообщений
     * @return ServiceEnum Сервис, связанный с действием
     */
    public static function getService(self $enum): ServiceEnum
    {
        return match ($enum) {
            self::BASE_CREATE => ServiceEnum::BASE_SERVICE
        };
    }

    /**
     * Возвращает конечную точку (endpoint) для указанного действия.
     *
     * @param self $enum Действие брокера сообщений
     * @return string Конечная точка, связанная с действием
     */
    public static function getEndpoint(self $enum): string
    {
        return match ($enum) {
            self::BASE_CREATE => 'create'
        };
    }

    /**
     * Возвращает HTTP-метод для указанного действия.
     *
     * @param self $enum Действие брокера сообщений
     * @return string HTTP-метод (например, POST, GET), связанный с действием
     */
    public static function getHttpMethod(self $enum): string
    {
        return match ($enum) {
            self::BASE_CREATE => "POST"
        };
    }
}

<?php

namespace App\Infrastructure\HttpMessagingBroker\Enums;

use bobrovva\enum_helper_lib\EnumHelper;
use Illuminate\Support\Facades\Crypt;

/**
 * Перечисление ServiceEnum
 *
 * Это перечисление содержит различные сервисы и методы для работы с их конфигурацией.
 */
enum ServiceEnum: string
{
    use EnumHelper;

    /**
     * Базовый сервис.
     */
    case BASE_SERVICE = 'base_service';

    /**
     * Возвращает имя сервиса.
     *
     * @param self $enum Экземпляр перечисления.
     * @return string Имя сервиса.
     */
    public static function getName(self $enum): string
    {
        return match ($enum) {
            self::BASE_SERVICE => 'Base service'
        };
    }

    /**
     * Возвращает ключ для конфигурации, который используется для получения параметров сервиса.
     * Убирает суффикс "_SERVICE" и переводит оставшуюся часть в нижний регистр.
     *
     * @param self $enum Экземпляр перечисления.
     * @return string Ключ для конфигурации.
     */
    private static function getConfigKey(self $enum): string
    {
        return strtolower(str_replace('_SERVICE', '', $enum->name));
    }

    /**
     * Возвращает URL для указанного сервиса из конфигурации.
     *
     * @param self $enum Экземпляр перечисления.
     * @return string URL сервиса.
     * @throws \RuntimeException Если URL не задан.
     */
    public static function getUrl(self $enum)
    {
        $key = self::getConfigKey($enum);
        $url = config("service.{$key}.url");

        throw_if(empty($url), self::getName($enum) . ' url empty');

        return $url;
    }

    /**
     * Возвращает токен для указанного сервиса, зашифрованный с помощью Crypt.
     *
     * @param self $enum Экземпляр перечисления.
     * @return string Зашифрованный токен.
     * @throws \RuntimeException Если токен не задан.
     */
    public static function getToken(self $enum): string
    {
        $key = self::getConfigKey($enum);
        $token = config("service.{$key}.token");

        throw_if(empty($token), self::getName($enum) . ' token empty');

        return Crypt::encryptString($token);
    }

    /**
     * Возвращает количество повторных попыток (retry) для указанного сервиса из конфигурации.
     *
     * @param self $enum Экземпляр перечисления.
     * @return int Количество повторных попыток.
     */
    public static function getRetry(self $enum): int
    {
        $key = self::getConfigKey($enum);
        $retry = config("service.{$key}.retry");

        return intval($retry);
    }

    /**
     * Возвращает время ожидания между попытками (retry_timeout) для указанного сервиса из конфигурации.
     *
     * @param self $enum Экземпляр перечисления.
     * @return int Время ожидания между попытками в секундах.
     */
    public static function getRetryTimeout(self $enum): int
    {
        $key = self::getConfigKey($enum);
        $retryTimeout = config("service.{$key}.retry_timeout");

        return intval($retryTimeout);
    }
}


<?php

namespace App\Test;

use App\Infrastructure\HttpMessagingBroker\Enums\HttpMessagingBrokerEnum;
use bobrovva\http_messaging_broker_lib\Facades\HttpMessagingBroker;

/**
 * Класс BaseClass
 *
 * Этот класс содержит действие для отправки данных через HTTP брокер сообщений.
 */
class BaseClass
{
    /**
     * Выполняет действие отправки данных через HTTP брокер сообщений.
     *
     * Метод отправляет тестовые данные с помощью брокера сообщений на заданный эндпоинт.
     *
     * @return void
     */
    public function actionSend(): void
    {
        $result = HttpMessagingBroker::send(
            HttpMessagingBrokerEnum::BASE_CREATE,
            [
                'test' => 'test',
            ]
        );
    }
}
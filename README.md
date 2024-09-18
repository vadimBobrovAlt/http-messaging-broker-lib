### Библиотека http messaging broker

Данная библиотека служит для взаимодействия между сервисами по http

Опубликовать конфигурацию
```
php artisan vendor:publish --tag=http-messaging-broker
```
После публикации создадутся 3 файла:
- `app/Infrastructure/MessagingBroker/Enums/ServiceEnum.php` - Enum сервисов с которыми система взаимодействует
- `app/Infrastructure/MessagingBroker/Enums/HttpMessagingBrokerEnum.php` - Enum эндпоинтов с которыми сервис взаимодействует
- `config/service.php` - Конфигурация сервисов

Библиотека содержит `Middleware` для зашиты эндпоинтов с которыми сервисы могут взаимодействовать работает по заголовку `X-Token`
```phpt
use bobrovva\http_messaging_broker_lib\Middleware\CheckServiceAuthMiddleware;

CheckServiceAuthMiddleware::class
```

Пример отправки

```phpt
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
```

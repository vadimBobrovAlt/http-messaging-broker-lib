<?php

namespace bobrovva\http_messaging_broker_lib\Facades;

use App\Infrastructure\HttpMessagingBroker\Enums\HttpMessagingBrokerEnum;
use bobrovva\http_messaging_broker_lib\HttpMessagingBrokerInterface;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array send(HttpMessagingBrokerEnum $endpoint, array $payload = [], array $options = []): array
 *
 * @see \bobrovva\http_messaging_broker_lib\HttpMessagingBrokerInterface
 */
class HttpMessagingBroker extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return HttpMessagingBrokerInterface::class;
    }
}

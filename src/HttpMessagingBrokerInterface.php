<?php

namespace bobrovva\http_messaging_broker_lib;

use App\Infrastructure\HttpMessagingBroker\Enums\HttpMessagingBrokerEnum;

interface HttpMessagingBrokerInterface
{
    public function send(HttpMessagingBrokerEnum $endpoint, array $payload = [], array $options = []): array;
}

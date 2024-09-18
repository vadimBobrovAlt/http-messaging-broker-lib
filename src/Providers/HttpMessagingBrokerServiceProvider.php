<?php

namespace bobrovva\messaging_broker_lib\Providers;

use bobrovva\http_messaging_broker_lib\HttpMessagingBroker\HttpBroker;
use bobrovva\http_messaging_broker_lib\HttpMessagingBroker\HttpMessagingBrokerInterface;
use Illuminate\Support\ServiceProvider;


class HttpMessagingBrokerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(HttpMessagingBrokerInterface::class, fn($app) => new HttpBroker());
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../Enums/HttpMessagingBrokerEnum.php' => base_path('app/Infrastructure/HttpMessagingBroker/Enums/HttpMessagingBrokerEnum.php'),
            __DIR__ . '/../Enums/ServiceEnum.php' => base_path('app/Infrastructure/HttpMessagingBroker/Enums/ServiceEnum.php'),
            __DIR__.'/../config/service.php' => config_path('service.php'),
        ], 'http-messaging-broker');
    }
}

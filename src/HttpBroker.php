<?php

namespace bobrovva\http_messaging_broker_lib;

use App\Infrastructure\HttpMessagingBroker\Enums\HttpMessagingBrokerEnum;
use App\Infrastructure\HttpMessagingBroker\Enums\ServiceEnum;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use InvalidArgumentException;
use PHPUnit\TextUI\XmlConfiguration\Exception;

/**
 * Класс HttpBroker реализует интерфейс HttpMessagingBrokerInterface и предоставляет методы для отправки HTTP-запросов
 * и обработки ответов от различных сервисов.
 */
class HttpBroker implements HttpMessagingBrokerInterface
{
    /**
     * Отправляет HTTP-запрос на указанный конечный пункт.
     *
     * @param HttpMessagingBrokerEnum $endpoint Конечный пункт запроса.
     * @param array $payload Данные, которые отправляются в запросе.
     * @param array $options Опции для конфигурации запроса.
     * @return array Ответ сервиса в формате массива.
     * @throws InvalidArgumentException Если метод HTTP-запроса недопустим.
     */
    public function send(HttpMessagingBrokerEnum $endpoint, array $payload = [], array $options = []): array
    {
        $service = HttpMessagingBrokerEnum::getService($endpoint);

        $httpClient = Http::baseUrl($this->getUrl($endpoint, $service, $payload))
            ->withHeaders($this->getHeaders($service, $payload))
            ->retry(ServiceEnum::getRetry($service), ServiceEnum::getRetryTimeout($service));

        return $this->handle($httpClient, $endpoint, $payload);
    }

    /**
     * Обрабатывает запрос в зависимости от HTTP-метода.
     *
     * @param PendingRequest $httpClient Клиент для выполнения HTTP-запросов.
     * @param HttpMessagingBrokerEnum $endpoint Конечный пункт запроса.
     * @param array $payload Данные запроса.
     * @return array Ответ сервиса в формате массива.
     * @throws InvalidArgumentException Если метод HTTP-запроса недопустим.
     */
    private function handle(PendingRequest $httpClient, HttpMessagingBrokerEnum $endpoint, array $payload): array
    {
        $httpMethod = HttpMessagingBrokerEnum::getHttpMethod($endpoint);

        $methods = [
            'GET' => 'handleGet',
            'POST' => 'handlePost',
            'PUT' => 'handlePut',
            'DELETE' => 'handleDelete',
        ];

        if (!isset($methods[$httpMethod])) {
            throw new InvalidArgumentException('Invalid HTTP method');
        }

        return call_user_func([$this, $methods[$httpMethod]], $httpClient, $payload);
    }

    /**
     * Обрабатывает GET-запрос.
     *
     * @param PendingRequest $httpClient Клиент для выполнения HTTP-запросов.
     * @return array Ответ сервиса в формате массива.
     */
    protected function handleGet(PendingRequest $httpClient): array
    {
        $response = $httpClient->get('');
        return $this->handleHttpResponse($response);
    }

    /**
     * Обрабатывает POST-запрос.
     *
     * @param PendingRequest $httpClient Клиент для выполнения HTTP-запросов.
     * @param array $payload Данные запроса.
     * @return array Ответ сервиса в формате массива.
     */
    protected function handlePost(PendingRequest $httpClient, array $payload): array
    {
        $response = $httpClient->post('', $payload['body'] ?? $payload);
        return $this->handleHttpResponse($response);
    }

    /**
     * Обрабатывает PUT-запрос.
     *
     * @param PendingRequest $httpClient Клиент для выполнения HTTP-запросов.
     * @param array $payload Данные запроса.
     * @return array Ответ сервиса в формате массива.
     */
    protected function handlePut(PendingRequest $httpClient, array $payload): array
    {
        $response = $httpClient->put('', $payload['body'] ?? $payload);
        return $this->handleHttpResponse($response);
    }

    /**
     * Обрабатывает DELETE-запрос.
     *
     * @param PendingRequest $httpClient Клиент для выполнения HTTP-запросов.
     * @param array $payload Данные запроса.
     * @return array Ответ сервиса в формате массива.
     */
    protected function handleDelete(PendingRequest $httpClient, array $payload): array
    {
        $response = $httpClient->delete('', $payload);
        return $this->handleHttpResponse($response);
    }

    /**
     * Возвращает заголовки для запроса.
     *
     * @param ServiceEnum $service Сервис, к которому отправляется запрос.
     * @param array $payload Данные запроса.
     * @return array Массив заголовков.
     */
    protected function getHeaders(ServiceEnum $service, array $payload): array
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-Token' => ServiceEnum::getToken($service)
        ];

        if (isset($payload['request_id'])) {
            $headers['X-Request-ID'] = $payload['request_id'];
        }

        return $headers;
    }

    /**
     * Формирует URL для запроса.
     *
     * @param HttpMessagingBrokerEnum $endpoint Конечный пункт запроса.
     * @param ServiceEnum $service Сервис, к которому отправляется запрос.
     * @param array $payload Данные запроса.
     * @return string URL для запроса.
     * @throws Exception Если параметр не найден в нагрузке.
     */
    protected function getUrl(HttpMessagingBrokerEnum $endpoint, ServiceEnum $service, array $payload): string
    {
        $baseUrl = ServiceEnum::getUrl($service);

        $endpoint = HttpMessagingBrokerEnum::getEndpoint($endpoint);
        $baseUrl .= preg_replace_callback('/\{(\w+)\}/', function ($matches) use ($baseUrl, $payload, $endpoint) {
            if (!isset($payload['params'][$matches[1]])) {
                throw new Exception("Parameter '{$matches[1]}' not found in payload for endpoint: {$baseUrl}{$endpoint}");
            }

            return $payload['params'][$matches[1]];
        }, $endpoint);

        $baseUrl .= isset($payload['query']) ? '?' . http_build_query($payload['query']) : '';
        return $baseUrl;
    }

    /**
     * Обрабатывает HTTP-ответ, проверяет статус и выбрасывает исключение в случае ошибки.
     *
     * @param Response $response Ответ HTTP-запроса.
     * @return array Ответ сервиса в формате массива.
     * @throws Exception Если запрос завершился ошибкой.
     */
    protected function handleHttpResponse(Response $response): array
    {
        if ($response->failed()) {
            $errorMessage = $response->json('message', 'Неизвестная ошибка');
            throw new Exception("Ошибка при выполнении запроса: $errorMessage");
        }

        return $response->json();
    }
}

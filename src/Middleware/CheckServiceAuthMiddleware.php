<?php

namespace bobrovva\http_messaging_broker_lib\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

/**
 * Middleware для проверки авторизации сервиса по заголовку токена.
 *
 * Этот middleware проверяет наличие и корректность заголовка `X-Token` в запросе.
 *
 * Если токен отсутствует или не совпадает с конфигурационным значением, возвращается ошибка 401 (Unauthorized).
 */
class CheckServiceAuthMiddleware
{
    /**
     * Обрабатывает входящий HTTP-запрос.
     *
     * @param  \Illuminate\Http\Request  $request  Входящий HTTP-запрос.
     * @param  \Closure  $next  Следующий обработчик в цепочке.
     * @return mixed
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException Если токен отсутствует или некорректен.
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->headers->get('X-Token');
        abort_if(!$token, 401, 'Invalid token header');

        if (Crypt::decryptString($token) !== config('service.token')) {
            abort(401, 'Invalid token');
        }

        return $next($request);
    }
}

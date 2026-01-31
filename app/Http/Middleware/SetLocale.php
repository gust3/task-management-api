<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // Получаем язык из заголовка Accept-Language
        $locale = $request->header('Accept-Language', 'ru');

        // Ограничиваем доступные языки
        $availableLocales = ['en', 'ru'];

        if (!in_array($locale, $availableLocales)) {
            $locale = 'ru'; // Язык по умолчанию
        }

        // Устанавливаем локаль
        App::setLocale($locale);

        return $next($request);
    }
}
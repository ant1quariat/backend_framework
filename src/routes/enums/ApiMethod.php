<?php

namespace skygoose\backend_framework\routes\enums;

final class ApiMethod
{
    /**
     * Получение строкового метода из перечисления
     * ___
     * @param Method $method http-метод
     * @return string значение конвертированного метода
     */
    public static function adaptToString(Method $method): string {
        return match ($method) {
            Method::POST => "POST",
            Method::PUT => "PUT",
            Method::PATCH => "PATCH",
            Method::DELETE => "DELETE",
            default => "GET"
        };
    }

    /**
     * Конвертация строкового типа запроса в перечисление enum
     * ___
     * @param string $method название http-метода
     * @return Method значение конвертированного метода
     */
    public static function adaptToMethod(string $method): Method {
        return match (strtoupper($method)) {
            "POST" => Method::POST,
            "PUT" => Method::PUT,
            "PATCH" => Method::PATCH,
            "DELETE" => Method::DELETE,
            default => Method::GET
        };
    }

    /**
     * Сравнивание передаваемого метода с методом из `$_SERVER['REQUEST_METHOD']`
     * ___
     * @param Method $method название http-метода из перечисления enum
     * @return bool ложь, если методы не совпадают
     */
    public static function instanceof(Method $method): bool {
        if(self::adaptToString($method) == $_SERVER['REQUEST_METHOD']) return true;
        return false;
    }

    /**
     * Конвертация строкового типа запроса в числовой
     * Поддерживаются только `POST` & `PUT`
     * ___
     * @param string|Method $method название http-метода или значение enum
     * @return int числовой индекс метода
     */
    public static function adaptToCurl(string|Method $method): int  {
        if($method instanceof Method) {
            return match ($method) {
                Method::PUT => CURLOPT_PUT,
                default => CURLOPT_POST
            };
        }
        return match (strtoupper($method)) {
          "POST" => CURLOPT_POST,
          "PUT" => CURLOPT_PUT,
        };
    }

    /**
     * Конвертация числового типа метода запроса в строковый
     * Поддерживаются только `POST (CURLOPT_POST)` & `PUT (CURLOPT_PUT)`
     * ___
     * @param int $method числовое значение cURL метода
     * @return string значение метода
     */
    public static function adaptFromInt(int $method): string  {
        return match ($method) {
            CURLOPT_PUT => "PUT",
            default => "POST"
        };
    }

}
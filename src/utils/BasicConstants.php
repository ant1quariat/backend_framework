<?php

namespace skygoose\backend_framework\utils;

final class BasicConstants
{
    private static int $flags = JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK;

    /**
     * @return int
     */
    public static function getFlags(): int
    {
        return self::$flags;
    }

    public static function getProtocol(): string {
            return isset($_SERVER['HTTP_SCHEME']) ? $_SERVER['HTTP_SCHEME'] : (
            (
                (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || 443 == $_SERVER['SERVER_PORT']
                ) ? 'https://' : 'http://'
            );
    }
}
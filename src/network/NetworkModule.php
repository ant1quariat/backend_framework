<?php

namespace skygoose\backend_framework\network;

final class NetworkModule
{
    private static array $modules = [];

    public static function registerModule(ISocialNetwork $network) : void {
        self::$modules[$network::class] = $network;
    }

    public static function getModule(string $clazzName) : ISocialNetwork|null {
        return  self::$modules[$clazzName] ?: null;
    }
}
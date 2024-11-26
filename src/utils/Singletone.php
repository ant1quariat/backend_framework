<?php

namespace skygoose\backend_framework\utils;

class Singletone
{
    protected static self|null $INSTANCE = null;
    protected function __construct(){}

    final public function __wakeup(): void {}
    final public function __clone(): void {}

    public static function getInstance(): ?static {
        if(static::$INSTANCE == null) static::$INSTANCE = new static();
        return static::$INSTANCE;
    }
}
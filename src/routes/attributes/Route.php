<?php

namespace skygoose\backend_framework\routes\attributes;


use skygoose\backend_framework\routes\enums\Method;

#[\Attribute(flags: \Attribute::TARGET_METHOD)]
final class Route
{
    public function __construct(
        public string $route,
        public Method|array $methods,
        public bool $multy = false
    )
    {}
}
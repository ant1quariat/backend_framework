<?php

namespace skygoose\backend_framework\templates;

final class Template
{
    private static array $GLOBAL_VARS = [];
    private const PATTERNS = [
        "COMMENT_PATTERN" => '/<!---#.*?#--->/s',
        "VARS_TAG_PATTERN" => '/<vars>(.*?)<\/vars>/s',
        "VARS_VAL_PATTERN" => '/\$(\w+)\s*=\s*(.*?);/'
    ];

    public static function setGlobalVar(string $key, mixed $value): void {
        self::$GLOBAL_VARS[$key] = $value;
    }

    public static function getGlobalVars(): array {
        return self::$GLOBAL_VARS;
    }

    public static function getPattern(string $key): string {
        return self::PATTERNS[$key] ?: "null";
    }

}
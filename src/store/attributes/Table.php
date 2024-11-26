<?php

namespace skygoose\backend_framework\store\attributes;

#[\Attribute(flags: \Attribute::TARGET_PROPERTY)]
final class Table
{
    public function __construct(
     public string $tableName,
     public string $query
    ){}
}
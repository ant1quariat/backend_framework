<?php

namespace skygoose\backend_framework\store\attributes;

use skygoose\backend_framework\store\BaseSchema;

final class ReflectDataAttribute
{
    public static function preparedAttributes(BaseSchema $schema): array {

        $reflectClazz = new \ReflectionClass($schema);
        $attributesData = [];

        foreach ($reflectClazz->getProperties() as $val) {
            foreach ($val->getAttributes() as $attribute) {
                if($attribute->getName() == Table::class) {
                    $tableAttribute = $attribute->newInstance();
                    $attributesData[$tableAttribute->tableName] = $tableAttribute->query;
                }
            }
        }

        return $attributesData;
    }
}
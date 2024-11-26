<?php

namespace skygoose\backend_framework\utils;

final class ReflectUtils
{
    public static function getClassesInNamespace($namespace): array
    {
        $files = scandir($_SERVER['DOCUMENT_ROOT'] . '/src/' . $namespace);
        $classes = array_map(function($file) use ($namespace){
            return $namespace . '\\' . str_replace('.php', '', $file);
        }, $files);

        return array_filter($classes, function($possibleClass){
            return class_exists($possibleClass);
        });
    }

    public static function getClassesInDir(string $dir): array {
        if(!is_dir($dir)) return [];
        $files = [];

        foreach (scandir($dir) as $item) {
            if(!in_array($item, ["..", ".", ".htaccess", ".htpasswd"]))
                array_push($files, $item);
        }

        return $files;
    }

    /**
     * Получение классов по их пространству имён (пути)
     * @param string $namespace Пространство имён
     * @return array ассоциативный массив с методами и атрибутами каждого класса
     * @throws \ReflectionException
     */
    public static function reflectClasses(string $namespace = 'controllers'): array {
        $dir = self::getClassesInNamespace($namespace);
        $classes = [];
        foreach ($dir as $className) {
            $reflectionClass = new \ReflectionClass($className);
            $classes[$reflectionClass->getName()] = [];

            $methods = $reflectionClass->getMethods();
            foreach ($methods as $method) {
                $classes[$reflectionClass->getName()][$method->getName()] = [];
                $attributes = $method->getAttributes();
                foreach ($attributes as $attribute) {
                    $name = str_replace("\\", "/", $attribute->getName());
                    $classes[$reflectionClass->getName()][$method->getName()][$name] = $attribute->getArguments();

                }

            }
        }

        return $classes;
    }

    /**
     * Получение классов по их пространству имён (пути) и атрибуту
     * @param string $namespace Пространство имён
     * @param string $attr Имя атрибута (Attribute::class)
     * @return array ассоциативный массив с методами и инстансом каждого класса
     * @throws \ReflectionException
     */
    public static function reflectClassesAsAttribute(string $namespace, string $attr): array {
        $dir = self::getClassesFromClass($namespace);
        $classes = [];
        foreach ($dir as $className) {
            $reflectionClass = new \ReflectionClass($className()->newInstance());

            $methods = $reflectionClass->getMethods();
            foreach ($methods as $method) {
                $attributes = $method->getAttributes();
                foreach ($attributes as $attribute) {
                    if($attribute->getName() == $attr) {
                        $classes[$reflectionClass->getName()] = [
                            "methods" => $methods,
                            "object" => $reflectionClass->newInstance()
                        ];
                    }

                }

            }
        }

        return $classes;
    }

    private static function getClassInstance($namespace) {
        if (class_exists($namespace)) {
            return new $namespace();
        } else {
            throw new Exception("Класс с пространством имен '{$namespace}' не найден.");
        }
    }

    private static function getClassesFromClass($clazz) {
        $clazzReflect = new \ReflectionClass($clazz);
        $path = str_replace("\\" . $clazzReflect->getName() . '.php', '', $clazzReflect->getFileName());

        return self::getClassesInDir($path);
    }

    public static function reflectDirClassesAsAttribute(string $path, string $attr): array {
        $dir = self::getClassesInDir($path);

        $classes = [];
        foreach ($dir as $className) {
            $reflectionClass = new \ReflectionClass($className);

            $methods = $reflectionClass->getMethods();
            foreach ($methods as $method) {
                $attributes = $method->getAttributes();
                foreach ($attributes as $attribute) {
                    if($attribute->getName() == $attr) {
                        $classes[$reflectionClass->getName()] = [
                            "methods" => $methods,
                            "object" => $reflectionClass->newInstance()
                        ];
                    }

                }

            }
        }

        return $classes;
    }

    public static function headersToString(array $headers): string {
        $header = "";

        foreach ($headers as $key => $val) { $header .= "{$key}: $val\r\n"; }
        return $header;
    }
}
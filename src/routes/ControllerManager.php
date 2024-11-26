<?php

namespace skygoose\backend_framework\routes;

use skygoose\backend_framework\routes\attributes\Route;
use skygoose\backend_framework\routes\enums\ApiMethod;
use skygoose\backend_framework\utils\ReflectUtils;

final class ControllerManager
{
    /**
     * Запуск менеджера контроллеров
     *
     * ### Пример:
     * ```php
     *  <?php
     *  namespace core\controllers;
     *  use skygoose\backend_framework\routes\BaseController;
     *  use skygoose\backend_framework\routes\enums\ApiMethod;
     *  use skygoose\backend_framework\routes\attributes\Route;
     *
     *  class SimpleController extends BaseController {
     *      #[Route(route: 'test', ApiMethod::GET)]
     *      private function test(): void {
     *
     *      }
     *  }
     * ?>
     * <?php
     *  //index.php
     *  require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
     *  use skygoose\backend_framework\routes\ControllerManager;
     *
     *  ControllerManager::runAsNamespace("core/controllers");
     * ?>
     * ```
     *
     * @param string $namespace пространство имён контроллеров
     * @return void
     */
    public static function runAsNamespace(string $namespace): void {
        try {
            $classes = ReflectUtils::reflectClassesAsAttribute($namespace, Route::class);
            foreach ($classes as $key => $val) { self::registerController($val["object"]); }
        } catch (\ReflectionException $ex) {}
    }

    /**
     * Запуск менеджера контроллеров
     *
     * ### Пример:
     * ```php
     *  <?php
     *  namespace core\controllers;
     *  use skygoose\backend_framework\routes\BaseController;
     *  use skygoose\backend_framework\routes\enums\ApiMethod;
     *  use skygoose\backend_framework\routes\attributes\Route;
     *
     *  class SimpleController extends BaseController {
     *      #[Route(route: 'test', ApiMethod::GET)]
     *      private function test(): void {
     *
     *      }
     *  }
     * ?>
     * <?php
     *  //index.php
     *  require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
     *  use skygoose\backend_framework\routes\ControllerManager;
     *
     *  ControllerManager::runAsDirectory("core/controllers");
     * ?>
     * ```
     *
     * @param string $path директория с контроллерами
     * @return void
     */
    public static function runAsDirectory(string $path): void {
        try {
            $classes = ReflectUtils::reflectDirClassesAsAttribute($path, Route::class);
            foreach ($classes as $key => $val) { self::registerController($val["object"]); }
        } catch (\ReflectionException $ex) {}
    }

    /**
     * Регистрация контроллеров для обработки ЧПУ-Роутинга
     * @param BaseController $controller Регистрируемый контроллер (экземпляр класса, наследующий интерфейс IController)
     * @return false|mixed
     * @throws \ReflectionException
     */
    public static function registerController(BaseController $controller): mixed {
        $reflection = new \ReflectionClass($controller);

        foreach ($reflection->getMethods() as $method) {
            $attributes = $method->getAttributes();

            foreach ($attributes as $attribute) {
                $routeAttribute = $attribute->newInstance();
                if(
                    str_starts_with($_SERVER['REQUEST_URI'], $routeAttribute->route . '/') ||
                    $routeAttribute->route == $_SERVER['REQUEST_URI'] ||
                    $routeAttribute->route == "*"
                ) {
                    if($routeAttribute->multy == true) {
                        if(is_array($routeAttribute->methods)) {
                            if(in_array(ApiMethod::adaptToMethod($_SERVER['REQUEST_METHOD']), $routeAttribute->methods)) {
                                $param = str_replace($routeAttribute->route, "", $_SERVER['REQUEST_URI']);
                                $params = array_filter(explode("/", $param),
                                    static function($var){
                                        return $var !== null || $var !== "";
                                    }
                                );

                                if(sizeof($params) <= 1) {
                                    $method->invoke($controller);
                                    return true;
                                } else {
                                    $method->invokeArgs($controller, [array_slice($params, 1)]);
                                    return true;
                                }

                            }
                        }

                        if(ApiMethod::instanceof($routeAttribute->methods)) {
                            $param = str_replace($routeAttribute->route, "", $_SERVER['REQUEST_URI']);
                            $params = array_filter(explode("/", $param),
                                static function($var){
                                    return $var !== null || $var !== "";
                                }
                            );

                            if(sizeof($params) <= 1) {
                                $method->invoke($controller);
                                return true;
                            } else {
                                $method->invokeArgs($controller, [array_slice($params, 1)]);
                                return true;
                            }

                        }

                    }
                    if(is_array($routeAttribute->methods)) {
                        if(in_array(ApiMethod::adaptToMethod($_SERVER['REQUEST_METHOD']), $routeAttribute->methods))
                            $method->invoke($controller);
                    }

                    else if(ApiMethod::instanceof($routeAttribute->methods)) { $method->invoke($controller); }

                    return true;
                }
            }
        }

        return false;
    }
}
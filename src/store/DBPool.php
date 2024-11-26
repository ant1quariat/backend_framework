<?php

namespace skygoose\backend_framework\store;

use skygoose\backend_framework\routes\http\exceptions\HttpException;
use skygoose\backend_framework\store\attributes\ReflectDataAttribute;

final class DBPool
{
    private static array $_DATABASES = [];

    /**
     * Добавление БД в пулл подключений
     * @param string $name Имя базы данных
     * @param array $config Конфигурация базы данных
     * @throws HttpException
     */
    public static function addDatabase(string $name, array $config) {
        try {
            if (self::checkConfig($name, $config)) {

                if (!key_exists($name, self::$_DATABASES)) {
                    self::$_DATABASES[$name] = self::createConnect($name, $config);
                }
            }
        } catch (\Exception $e) {
            throw new HttpException(500, "{$name} | Database error");
        }
    }

    /**
     * Инициализация базы данных и использование схемы.
     * @param string $name Имя БД
     * @param array $config Конфигурация БД
     * @param BaseSchema $schema Класс схемы БД
     * @throws HttpException
     */
    public static function makeDatabase(string $name, array $config, BaseSchema $schema) {
        try {
            if (self::checkConfig($name, $config)) {
                $dbInst = self::createConnect($name, $config);
                $dbData = ReflectDataAttribute::preparedAttributes($schema);

                if (sizeof($dbData) > 1) {
                    foreach ($dbData as $k => $v) {
                        $dbInst->prepare($v);
                    }
                }

                if (!key_exists($name, self::$_DATABASES)) {
                    self::$_DATABASES[$name] = $dbInst;
                }
            }
        } catch (\Exception $e) {
            throw new HttpException(500, "{$name} | Database error");
        }
    }


    private static function createConnect(string $name, array $config): Database|null {
        $cfg = [
            "driver" => $config['driver'] ?: "mysql",
            "host" => $config['host'] ?: "localhost",
            "port" => $config['port'] ?: 3307,
            "username" => $config['username'] ?: "root",
            "password" => $config['password'] ?: "",
            "dbname" => $name ?: "dbname",
        ];

        return new Database($cfg);
    }


    private static function checkConfig(string $name, array $config): bool {
        if(sizeof($config) < 4) throw new \Exception("{$name} | Invalid config!");
        if(!key_exists("driver", $config)) throw new \Exception("{$name} | Key 'driver' not found in DB-config!");

        if($config["driver"] == "sqlite") {
            return true;
        }

        if(!key_exists("host", $config)) throw new \Exception("{$name} | Key 'host' not found in DB-config!");
        if(!key_exists("port", $config)) throw new \Exception("{$name} | Key 'port' not found in DB-config!");
        if(!key_exists("username", $config)) throw new \Exception("{$name} | Key 'username' not found in DB-config!");
        if(!key_exists("password", $config)) throw new \Exception("{$name} | Key 'password' not found in DB-config!");

        return true;
    }

    /**
     * Получения базы данных из пула
     * @param string $name Имя БД
     * @return Database|false Если БД не зарегистрирована в пуле, то вернёт false
     */
    public static function getConnection(string $name): Database|false {
        if(self::$_DATABASES[$name] == null) return false;
        return self::$_DATABASES[$name];
    }


}
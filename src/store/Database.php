<?php

namespace skygoose\backend_framework\store;

final class Database
{
    private \PDO $dsn;
    public function __construct(
        protected readonly array $DB_CONFIG
    ){
        $this->dsn = new \PDO(
            "mysql:host=" . $DB_CONFIG["host"] .
            ";port=".$DB_CONFIG["port"] .
            ";dbname=" .$DB_CONFIG["dbname"],
            $DB_CONFIG["username"],
            $DB_CONFIG["password"]
        );
    }


    /**
     * @param string $sql Строка запроса.
     * @param array $data Ассоциативный массив с данными для подстановки.
     * @return array|false Данные или ложь при их отсутствии.
     */
    public function prepare(string $sql, array $data = []): array {
        $stm = $this->dsn->prepare($sql);

        if(empty($data)) {
            $stm->execute();
            return $stm->fetchAll(\PDO::FETCH_ASSOC);
        }
        $stm->execute($data);

        return $stm->fetchAll(\PDO::FETCH_ASSOC);
    }


    public function prepareBinds(string $sql): \PDOStatement|false {
        $stm = $this->dsn->prepare($sql);
        return $stm;
    }
}
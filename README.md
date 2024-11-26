Backend Framework
=================

[ - ] [История изменений](REALSES.md "Dev log")

В данную библиотеку включены основные инструментарии для удобной 
разработки Backend-части WEB-приложения или FullStack разработки.


***
<a id="navigate"></a>
### Навигация по документации:
- <a href="#requirements">Требования</a>.
- <a href="#install">Установка библиотеки</a>.
- <a href="#module-routes">Модуль Routes (маршрутизатор)</a>.
- - <a href="#module-routes-controller">Контроллеры</a>.
- - <a href="#module-routes-method">Перечисление Method</a>.
- - <a href="#module-routes-apimethod">Класс ApiMethod</a>.
- - <a href="#module-routes-controller-manager">Менеджер контроллеров</a>.
- - <a href="#module-routes-annotation-route">Атрибут(аннотация) Route</a>.
- - <a href="#module-routes-http">Подмодуль HTTP</a>.
- - - <a href="#module-routes-http-response">Класс Response</a>.
- - - <a href="#module-routes-http-exception">Класс HttpException</a>.
- <a href="#module-store">Модуль Store (хранилище)</a>
- - <a href="#module-store-database">Класс Database</a>
- - <a href="#module-store-dbpool">Класс DBPool</a>
- - <a href="#module-store-schema">Схемы базы данных</a>
- <a href="#module-templates">Модуль Templates (Шаблонизатор)</a>
- - <a href="#module-templates-file">Синтаксис шаблона (.tpl файл)</a>
- - <a href="#module-templates-templatebuilder">Билдер шаблона (TemplateBuilder)</a>
- - <a href="#module-templates-template">Класс Template (хранилище констант)</a>
- [?] [Модуль Network](#module-network "Модуль в разработке")
- - [?] [Класс HttpQueryBuilder](#module-network-vkhandler "Модуль в разработке")
- - [?] [Класс VKHandler](#module-network-vkhandler "Модуль в разработке")
- - [?] [Класс TelegramHandler](#module-network-dshandler "Модуль в разработке")
- - [?] [Класс DiscordHandler](#module-network-tghandler "Модуль в разработке")

___

Требования
----------

<table>
    <tr>
        <td colspan="2"><b>Компонент</b></td>
        <td><b>Версия</b></td>
        <td>Ссылка на установку</td>
    </tr>
    <tr>
        <td>1</td>
        <td>PHP</td>
        <td>>= 8.2 </td>
        <td><a href="https://www.php.net/downloads.php">Перейти на сайт</a></td>
    </tr>
    <a id="requirements"></a>
    <tr>
        <td>2</td>
        <td>Composer</td>
        <td>>= 2.5.5 </td>
        <td><a href="https://getcomposer.org/download/">Перейти на сайт</a></td>
    </tr>
    <tr>
        <td colspan="4"><small><i>Данные требования могут изменяться со временем</i></small></td>
    </tr>
</table>

<br>
<hr>

Установка
---------
 composer.json
```json
...,
"require": {
  "skygoose/backend_framework": "*"
},
        
"repositories": [
    {
        "type": "path",
        "url": "/path/to/local/lib"
    }

]
```
<a id="install"></a>
Обновление зависимостей
```bash
cd /path/to/project
```
```bash
composer update
```
<br>
<hr>


Модуль Routes
-------------

<a href="#navigate">^ К навигации</a>

Данный модуль предназначен для удобной маршрутизации на сайте.
В данном модуле рассмотрим его структуру и классы.
<a id="module-routes"></a>

Данный модуль будет обновляться и пополняться новыми фишками
в будущих обновлениях

___
### Модуль Routes > Контроллеры

<a href="#navigate">^ К навигации</a>

Контроллеры - основные действующие лица нашей маршрутизации.
Каждый новый контроллер обязан наследовать интерфейс `BaseController`.

Пример создания контроллера:
<a id="module-routes-controller"></a>
```php
namespace core\controllers;

use skygoose\backend_framework\routes\enums\Method;
use skygoose\backend_framework\routes\BaseController;
use skygoose\backend_framework\routes\attributes\Route;

final class SimpleController implements BaseController {
    #[Route(route: '/', methods: Method::GET)]
    private function home(): void {
        //code here
    }
}
```

В примере кода выше мы видем новые для нас атрибут `Route` и перечисление `Method`. 
Поговорим о них далее.
___
### Модуль Routes > Перечисление Method и обработчик ApiMethod
<a href="#navigate">^ К навигации</a>

Перечисление Method хранит в себе основные <i>HTTP</i>-Методы запросов, 
которые необходимы для построения хорошего `RESTFull API`.
Рассмотрим данное перечисление подробнее:
<a id="module-routes-method"></a>
```php
enum Method: string {
    case GET = "GET"; 
    case POST = "POST";
    case PUT = "PUT";
    case PATCH = "PATCH"; 
    case DELETE = "DELETE";
}
```

Прелесть данного перечисления раскрывает следующий класс `ApiMethod`
<a id="module-routes-apimethod"></a>

```php
// Пример в index.php

use skygoose\backend_framework\routes\enums\ApiMethod;
use skygoose\backend_framework\routes\enums\Method;

ApiMethod::get(Method::GET); // Получаем метод в виде строки
ApiMethod::adaptToMethod("get"); // Кастуем строковый метод в Enum-вид

/**
* Метод instanceof сравнивает значение $_SERVER['REQUEST_METHOD']
* и передаваемый метод в виде аргумента.
* Если текущий маршрут равен передаваемому, возвращает true
* Иначе - false
*/
ApiMethod::instanceof(Method::GET);
```
___
### Модуль Routes > Менеджер контроллеров.
<a href="#navigate">^ К навигации</a>

Менеджер контроллеров предназначен для подгрузки контроллеров с их последующей обработкой.
Текущий функционал позволяет загружать контроллеры посредством пространств имён (namespace),
абсолютного пути (path) и по отдельности.

<a id="module-routes-controller-manager"></a>
Рассмотрим пример использования:
```php
// index.php
use skygoose\backend_framework\routes\ControllerManager;

$simpleController = new MyController();

/**
* Метод регистрирует отдельный контроллер. В качестве параметра передаём
* инстанс(объект) нашего контроллера 
*/
ControllerManager::registerController($simpleController);

/**
* Метод регистрирует все контроллеры в директории автоматически. 
* В качестве параметра передаём абсолютный путь до директории с контроллерами.
* Данный метод временно не работает
*/
ControllerManager::runAsDirectory("/path/to/controller/dir");

/**
* Метод регистрирует все контроллеры автоматически. 
* В качестве параметра передаём пространство имён контроллеров
* Данный метод временно не работает
*/
ControllerManager::runAsDirectory("testing\\namespace\\controller");
```
***
### Модуль Routes > Атрибут Route

<a href="#navigate">^ К навигации</a>

Атрибуты в языке `PHP` довольно новая фишка. Благодаря данным атрибутам можно
сокращать написанный код. Аналогом данной фишки были `аннотации Java` и
`PHPDocs коментарии`.

Вернёмся к нашему атрибуту. Данный атрибут позволяет помечать методы классов
наших контроллеров. При нужном URL в адресной строке браузера и 
нужном методе запроса наш менеджер контроллеров
автоматически вызовет необходимый метод в контроллере.

Устройство атрибута:
```php
use skygoose\backend_framework\routes\enums\Method;
use skygoose\backend_framework\routes\attributes\Route;
use skygoose\backend_framework\routes\BaseController;

final class Controller implements BaseController {
    
    // Пример базового маршута в приложении
    #[Route(
        route: '/', // Маршрут, по которому отрабатывает наш метод
        methods: Method::GET // Метод обращения по данному маршруту
    )]
    protected function home(): void {
        echo "<h1>Привет мир!</h1>";
        exit();
    }
    
    // Пример маршрута авторизации с использованием нескольких методов
    #[Route(
        route: '/api/oauth',
        methods: [
            Method::POST, // В данном случае можно передавать
            Method::GET   // массив методов
        ]  
    )]    
    protected function oauth(): void {
        echo "<h1>Oauth route</h1>";
        exit();
    }
    
}
```
***

### Модуль Routes > Подмодуль HTTP.

<a href="#navigate">^ К навигации</a>

В данном подмодуле (в будущем отдельный модуль) существует всего лишь два класса,
которые можно использовать в нашем приложении. Рассмотрим эти классы детальнее
<a id="module-routes-http"></a>

***
#### Подмодуль HTTP > Класс Response
<a id="module-routes-http-response"></a>

<a href="#navigate">^ К навигации</a>

Данный класс отлично используется в связке с контроллерами. 
Он позволяет отправлять JSON и HTML ответы с необходимыми заголовками
и необходимым HTTP-Кодом ответа.

Рассмотрим детальнее:
```php
use skygoose\backend_framework\routes\enums\Method;
use skygoose\backend_framework\routes\http\Response;
use skygoose\backend_framework\routes\BaseController;
use skygoose\backend_framework\routes\attributes\Route;

final class SimpleController implements BaseController {

    #[Route(route: '/api/test', methods: 
        [Method::GET, Method::POST, Method::PUT, Method::PATCH, Method::DELETE]
    )]
    protected function api(): void {
        (new Response())         // Инициализируем ResponseBuilder
            ->setCode(200)       // Устанавливаем код ответа
            ->setData([          // Устанавливаем данные
                "message" => "ok"    
            ])
            ->setHeaders([       // Устанавливаем заголовки. Можно оставить пустым
                // Данный заголовок уже присутствует при отправке JSON-ответа
                "Content-Type": "text/json"
            ])
            ->buildAsJson();     // Отправляем ответ в виде JSON
    }
    
    
    // Статические методы класса
    #[Route(route: '/api', methods: 
        [Method::GET, Method::POST, Method::PUT, Method::PATCH, Method::DELETE]
    )]
    protected function api_test(): void {
        Response::ofHTML(
            // HTTP статус-код
            status: 200,
            // HTML-Разметка
            html: "<h1>HelloWorld</h1>",
            // Заголовки ответа сервера. Можно оставить пустым.
            // По стандарту во время отправки заголовок ниже
            // Уже отправляется
            headers: ["Content-Type" => "text/html"]
        );
        
        // Данный метод аналогичен билдеру
        Response::ofJSON(
            // HTTP статус-код
            status: 200,
            // Массив данных
            data: ["message" => "ok"],
            // Заголовки ответа сервера. Можно оставить пустым.
            // По стандарту во время отправки заголовок ниже
            // Уже отправляется
            headers: ["Content-Type" => "text/json"]
        );
    }

}
```
***

#### Подмодуль HTTP > Класс HttpException
<a id="module-routes-http-exception"></a>

<a href="#navigate">^ К навигации</a>

Данный класс-исключение используется при возникновении какой-либо ошибки.
Он содержит в себе все возможные статус-коды и сообщения HTTP-ошибок

Пример:
```php
use skygoose\backend_framework\routes\http\exceptions\HttpException;

function foo(int $num): void {
    // Выбрасываем исключение HttpException
    if($num < 3) throw new HttpException(500, "Ошибка сервера");
    
    // Статический вызов отправки исключения
    HttpException::print(500, "Внутренняя ошибка сервера");
}
```
***
# Модуль Store (Хранилище)
<a id="module-store"></a>

<a href="#navigate">^ К навигации</a>

Модуль хранилища представляет собой надстройку над `PDO (PHP Data Objects)`.
Реализованы подготовленные запросы, удобный их вызов при помощи одного метода,
пул баз данных.

***
### Модуль Store > Класс Database
<a id="module-store-database"></a>

<a href="#navigate">^ К навигации</a>

Класс базы данных реализуется при помощи вышеупомянутого `PDO`.
Данный класс позволит вам удобно работать с базами данных в вашем веб-приложении.

```php
// index.php
use skygoose\backend_framework\store\Database;

$config = [
    "driver" => "mysql",        // Драйвер подключения (mysql)
    "host" => "localhost",      // Айпи-адрес или домен хостинга БД
    "port" => 3306,             // Порт подключения (старнадт 3306)
    "username" => "root",       // Имя пользователя БД (стандарт root)
    "password" => "",           // Пароль пользователя БД (стандарт '')
    "dbname" => "test"          // Название базы данных
];

$dbInstance = new Database(config: $config);

// Выполняем запрос.
// Если происходит выборка, то возвращается массив данных
// Если никаких данных не найдено, то false
$data = $dbInstance->prepare(
    sql: "SELECT * FROM `users` WHERE `id` = :id", 
    data: [
        "id" => 1
    ]
);

// Выполняем запрос без подготовки
$data = $dbInstance->prepare(sql: "SELECT * FROM `users`;");
```

***
### Модуль Store > Класс DBPool

<a href="#navigate">^ К навигации</a>

Данный класс хранит в себе все подключения к базам данных. 
Если у вас несколько баз данных, то лучше всего использовать данный подход.

<a id="module-store-dbpool"></a>

```php
use skygoose\backend_framework\store\DBPool;

$configOne = [
    ...
    "dbname" => "foo"
];

$configTwo = [
    ...
    "dbname" => "bar"
];

// Регистрируем базы данных
DBPool::addDatabase(name: "foo", config: $configOne);
DBPool::addDatabase(name: "bar", config: $configTwo);


// Получение БД из пула
$dbInst = DBPool::getConnection(name: "foo"); // false, если БД нет в пуле

// Выполняем запрос так же как и в первом варианте
$data = $dbInst->prepare(sql: "SELECT * FROM `users`;");
```

***
### Модуль Store > Схемы Баз данных
<a id="module-store-schema"></a>

<a href="#navigate">^ К навигации</a>

Схемы баз данных довольно удобная тема при инициализации приложения.
Не нужно в индексе писать множество SQL-запросов на создание таблиц.

Каждая таблица описывается в специальном атрибуте `Table`, который помечает
поле класса.

```php
use skygoose\backend_framework\store\BaseSchema;
use skygoose\backend_framework\store\attributes\Table;

class SimpleSchema extends BaseSchema {
    #[Table(
        tableName: "users",
        query: "
            CREATE TABLE IF NOT EXISTS `users` (
                `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(15) NOT NULL,
                `email` VARCHAR(100) NOT NULL UNIQUE
            );
        " 
    )]
    protected string $users;
    
    #[Table(
        tableName: "groups",
        query: "
            CREATE TABLE IF NOT EXISTS `groups` (
                `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `tag` VARCHAR(100) NOT NULL UNIQUE,
                `permissions` TEXT
            );
        " 
    )]
    protected string $groups;
}
```

Далее добавляем нашу БД в пул подключений немного другим методом:
```php
use skygoose\backend_framework\store\DBPool;

$config = [
    ...
    "dbname" => "foo"
];

$schema = new SimpleSchema();

// Данный способ работает индентично вышеописанному,
// но в процессе добавления БД в пул подключений
// выполняются запросы из нашей схемы.
DBPool::makeDatabase(
    name: "foo", 
    config: $config, 
    schema: $schema
);
```
***

# Модуль Templates (Шаблонизатор)
<a id="module-templates"></a>

<a href="#navigate">^ К навигации</a>

Данный модуль реализует удобный шаблонизатор, включающий в себя
заменители (placehoder), переменные шаблона (vars), 
глобальные переменные шаблона (global vars).
***

### Модуль Templates > Файл шаблона

<a href="#navigate">^ К навигации</a>

Данная библиотека реализует свой синтаксис шаблонов, позволяющий 
встраивать различные значения в шаблон. Расширение шаблона должно быть `.tpl`.

1. Заменители (Placeholders)

- ```html
  <div>
      {$placeholder}
  </div>
    ```
<a id="module-templates-file"></a>
2. Переменные шаблона (vars)
- ```html
  <!---# 
    Объявление переменных.
    При сборке шаблона в вёрстку данный блок исчезнет
    из вёрстки. 
  #--->
  <vars>
    $var = "123";
  </vars>
  
  <!---# 
    Использование переменной 
  #--->
  <div>
      {%$var%}    
  </div>
    ```
3. Глобальные переменные шаблона (Global Vars)
- ```html
  <!---# 
    Глобальные переменные можно использовать в блоке
    переменных
  #--->
  <vars>
    $var = "123";
    $test = $_ASSETS;
  </vars>
  
  <!---# 
    Использование переменных
  #--->
  <div data-asset="$_ASSETS">
      {%$var%}    
  </div>  
  ```
4. Коментарии в шаблоне
- ```html
    <!---# 
      Как видно выше, комментарии похожи на HTML,
      но обрабатываются (не встраиваются в вёрску)
      только при конструкции с решёткой '#'
    #--->
  ```

***

### Модуль Templates > TemplateBuilder (Билдер шаблонов)

<a href="#navigate">^ К навигации</a>

Билдер шаблона обрабатывает все переменные, заменители и коментарии внутри
нашего шаблона и возвращает готовую вёрстку.

Шаблон:
<a id="module-templates-templatebuilder"></a>
```html
<vars>
    $user = "Alex";
</vars>
<div class="wrapper">
    {$navbar}
    <main class="main__block">
        <h1>Привет, {%$user%}</h1>
    </main>
    {$footer}
</div>
```
Обработка шаблона нашим билдером
```php
use skygoose\backend_framework\templates\TemplateBuilder;

// По стандарту директория шаблонов имеет следующий путь:
// templatesDir: DOCUMENT_ROOT/resources/templates/
// Где DOCUMENT_ROOT - корень проекта
echo (new TemplateBuilder(path: "template", templatesDir: "" /* Можно переопределить */ ))
    ->addPlaceholder(       // Препроцессинг заменителей
        key: "navbar",                        // Заменитель 
        value: "<nav><h3>Navbar</h3></nav>"   // Значение
    )
    ->addPlaceholders([     // Вариант с массивом заменителей
        "navbar" => "value",                  
        ...
    ])
    ->setPattern(           // Задаём вариант синтаксиса заменителя
        start: '{-$',
        end: '-}' 
    )
->build(                 // Собираем и печатаем готовую вёрску
    varPreprocess: true, // Управление процессом переменных (false - не обрабатывает их)
    clearComments: true  // Управление коментариев (false - не удаляет их)
);
```

***

### Модуль Templates > Класс Template 
<a id="module-templates-template"></a>

<a href="#navigate">^ К навигации</a>

Класс Template хранит в себе глобальные переменные, паттерны (RegEx) 
обработки переменных и коментариев.

```php
use skygoose\backend_framework\templates\Template;

// Получение списка глобальных переменных
Template::getGlobalVars();
// Установка/создание глобальной переменной
Template::setGlobalVar(key: "name", value: "value");

// Получение паттерна по ключу
Template::getPattern("COMMENT_PATTERN");      
```
***

### Модуль Network
<a id="module-network"></a>

<a href="#navigate">^ К навигации</a>

В разработке.

```php
     
```
***
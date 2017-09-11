<?php

use Phalcon\Di;
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;

ini_set("display_errors", 1);
error_reporting(E_ALL);

define("ROOT_PATH", __DIR__);

set_include_path(
    ROOT_PATH . PATH_SEPARATOR . get_include_path()
);

// Необходим для phalcon/incubator
include __DIR__ . "/../vendor/autoload.php";

// Используем автозагрузчик приложений для автозагрузки классов.
$loader = new Loader();

$loader->registerDirs(
    [
        ROOT_PATH,
    ]
);

$loader->register();

$di = new FactoryDefault();

Di::reset();

// Здесь можно добавить любые необходимые сервисы в контейнер зависимостей

Di::setDefault($di);
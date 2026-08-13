<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use core\Session;
use core\Request;
use core\Router;

error_reporting(E_ALL);

$config = require dirname(__DIR__) . '/config/app.php';

if ($config['app']['debug']) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

Session::start();

require dirname(__DIR__) . '/config/routes.php';

$request = new Request();
$router->dispatch($request->method(), $request->uri());
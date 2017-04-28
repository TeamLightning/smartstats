<?php

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__.'/../app/resources/views', [
        //'cache' => __DIR__ . '/../storage/cache/views'
    ]);

    $basePath = rtrim(str_ireplace('index.php', '',
        $container['request']->getUri()->getBasePath()), '/');

    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

    return $view;
};

$container['db'] = function ($container) {
    return new \Medoo\Medoo($container['settings']['db']);
};

$container['HomeController'] = function ($container) {
    return new \App\Controllers\HomeController($container);
};

$container['UserController'] = function ($container) {
    return new \App\Controllers\UserController($container);
};

$container['LoginHandler'] = function ($container) {
    return new \App\Authenticator\LoginHandler($container);
};

$container['SignUpHandler'] = function ($container) {
    return new \App\Authenticator\SignUpHandler($container);
};

$container['CookieHandler'] = function ($container) {
    return new \App\Authenticator\CookieHandler($container);
};
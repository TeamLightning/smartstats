<?php

$container['config'] = function ($container) {
    $dotenv =  new Symfony\Component\Dotenv\Dotenv();
    $dotenv->load(__DIR__ . '/../.env');

    return $dotenv;
};

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__.'/../app/resources/views', [
        //'cache' => __DIR__.'/../storage/cache/views',
    ]);

    $basePath = rtrim(str_ireplace('index.php', '',
        $container['request']->getUri()->getBasePath()), '/');

    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

    return $view;
};

$container['db'] = function ($container) {
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection([
        'driver' => getenv('DB_TYPE'),
        'host' => getenv('DB_HOST'),
        'database' => getenv('DB_NAME'),
        'username' => getenv('DB_USER'),
        'password' => getenv('DB_PASS'),
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    ]);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};

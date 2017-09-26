<?php

if(!file_exists(__DIR__ . '/../.env')) {
    if(!file_exists(__DIR__ . '/../.env.example')) {
        die('INSTALLATION ERROR! .env.example FILE NOT FOUND');
    } else {
        copy(__DIR__ . '/../.env.example', __DIR__ . '/../.env');
        die('CONFIGURATION FILE CREATED. PLEASE EDIT THE VALUES IN .env');
    }
}

$dotenv = new Symfony\Component\Dotenv\Dotenv();
$dotenv->load(__DIR__ . '/../.env');

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

$container['auth'] = function () {
    $db = new \PDO(getenv('DB_TYPE').":dbname=".getenv('DB_NAME').";host=".getenv('DB_HOST').";charset=utf8mb4",
        getenv('DB_USER'), getenv('DB_PASS'));

    return new \Delight\Auth\Auth($db);
};

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__.'/../app/resources/views'/*,
        [
            'cache' => __DIR__ . '/../cache'
        ]*/
    );

    $basePath = rtrim(str_ireplace('index.php', '',
        $container['request']->getUri()->getBasePath()), '/');

    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

    $view->getEnvironment()->addFunction(new Twig_Function('siteUrl', function () {

        return getenv('SITE_URL');
    }));

    $view->getEnvironment()->addFunction(new \Twig_Function('username', function () use ($container) {

        return $container->auth->getUsername();
    }));

    $view->getEnvironment()->addFunction(new Twig_Function('loggedIn', function () {

        return isset($_SESSION) && isset($_SESSION['auth_logged_in']) && $_SESSION['auth_logged_in'] === true;
    }));

    return $view;
};

$container['db'] = function ($capsule) {
    return $capsule;
};

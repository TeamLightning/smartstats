<?php

require __DIR__.'/../vendor/autoload.php';

$app = new \Slim\App([
    'settings'  => [
        'displayErrorDetails' => true,

        'db'    => [
            'database_name' => 'stats',
            'database_type' => 'mysql',
            'server'        => 'localhost',
            'username'      => 'root',
            'password'      => 'amaramar',
            'charset'       => 'utf8',
        ],
    ],
]);

$container = $app->getContainer();

require 'container.php';
require 'route.php';

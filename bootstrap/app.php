<?php

require __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
    ],
]);

$container = $app->getContainer();

require 'container.php';
require 'middleware.php';
require __DIR__ . '/../app/routes/web.php';

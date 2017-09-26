<?php

require __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => getenv('SITE_MODE', false),
    ],
]);

$container = $app->getContainer();

require 'container.php';
require __DIR__ . '/../app/routes/web.php';

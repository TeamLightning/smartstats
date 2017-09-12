<?php

use App\Handler\StatusIndexer;
use App\Authenticator\AuthHandler;
use App\Controllers\HomeController;
use App\Controllers\UserController;
use App\Middlewares\UserMiddleware;
use App\Middlewares\GuestMiddleware;

$app->get('/', HomeController::class . ':index')->setName('index');

$app->group('/auth', function () use ($container) {
    $this->get('/login', HomeController::class . ':login')
        ->setName('auth.login');
    $this->get('/register', HomeController::class . ':register')
        ->setName('auth.register');
    $this->get('/verify/{selector}/{token}', AuthHandler::class . ':verifyMail')
        ->setName('auth.mail.check');

    $this->post('/login', AuthHandler::class . ':login')
        ->setName('auth.login.post');
    $this->post('/register', AuthHandler::class . ':register')
        ->setName('auth.register.post');
})->add(new GuestMiddleware($container));

$app->group('/user', function () use ($container) {
    $this->get('/logout', AuthHandler::class . ':logout')->setName('logout');
    $this->get('/home', UserController::class . ':home')->setName('user.home');
    $this->get('/trash', UserController::class . ':trash')->setName('user.trash');
    $this->get('/offline', UserController::class . ':offlineServers')->setName('user.offline');
    $this->get('/online', UserController::class . ':onlineServers')->setName('user.online');
})->add(new UserMiddleware($container));

$app->group('/cron/index', function()
{
    $this->get('/free', StatusIndexer::class . ':indexServerFree');
    $this->get('/silver', StatusIndexer::class . ':indexServerSilver');
    $this->get('/gold', StatusIndexer::class . ':indexServerGold');
    $this->get('/diamond', StatusIndexer::class . ':indexServerDiamond');
});

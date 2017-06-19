<?php

use App\Authenticator\AuthHandler;
use App\Controllers\HomeController;
use App\Controllers\UserController;
use App\Middlewares\UserMiddleware;
use App\Middlewares\GuestMiddleware;

$app->get('/', HomeController::class . ':index')->setName('index');

$app->group('/auth', function () {
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

$app->group('/user', function () {

    $this->get('/home', UserController::class . ':home')->setName('user.home');
})->add(new UserMiddleware($container));
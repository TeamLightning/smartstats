<?php

$app->get('/', \App\Controllers\HomeController::class . ':index')->setName('index');

$app->group('/auth', function () {
    $this->get('/login', \App\Authenticator\AuthHandler::class . ':login')
        ->setName('auth.login');
    $this->get('/signup', \App\Authenticator\AuthHandler::class . ':signup')
        ->setName('auth.signup');
    $this->get('/verify/{selector}/{token}', \App\Authenticator\AuthHandler::class . ':verifyMail')
        ->setName('auth.mail.check');

    $this->post('/login', \App\Authenticator\AuthHandler::class . ':login')
        ->setName('auth.login.post');
});
<?php

$app->get('/', 'HomeController:index')
    ->add(new \App\Middlewares\GuestMiddleware($container))
    ->setName('index');

$app->group('/auth', function() use ($container) {
    $this->get('/logout', function ($req, $res, $args) {
        session_destroy();
        unset($_SESSION);
        return $res->withHeader('Location', '/');
    })->add(new \App\Middlewares\UserMiddleware($container))
        ->setName('auth.logout');
    $this->get('/login', 'HomeController:login')
        ->add(new \App\Middlewares\GuestMiddleware($container))
        ->setName('auth.login');
    $this->get('/signup', 'HomeController:signup')
        ->add(new \App\Middlewares\GuestMiddleware($container))
        ->setName('auth.signup');

    $this->post('/login', 'HomeController:getlogin')
        ->add(new \App\Middlewares\GuestMiddleware($container))
        ->setName('auth.login.post');
    $this->post('/signup', 'HomeController:getsignup')
        ->add(new \App\Middlewares\GuestMiddleware($container))
        ->setName('auth.signup.post');
});

$app->group('/user', function () use ($container) {
    $this->get('/home', 'UserController:home')
        ->add(new \App\Middlewares\UserMiddleware($container))
        ->setName('user.home');
    $this->get('/show', 'UserController:show')
        ->add(new \App\Middlewares\UserMiddleware($container))
        ->setName('user.show');
    $this->get('/create', 'UserController:create')
        ->add(new \App\Middlewares\UserMiddleware($container))
        ->setName('user.create');

    $this->post('/create', 'UserController:showCreate')
        ->add(new \App\Middlewares\UserMiddleware($container))
        ->setName('user.create.post');
});
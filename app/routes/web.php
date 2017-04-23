<?php

$app->get('/', 'HomeController:index')
    ->add(new \App\Middlewares\GuestMiddleware($container))
    ->setName('index');

$app->group('/auth', function() use ($container) {
    $this->get('/login', 'HomeController:login')
        ->add(new \App\Middlewares\GuestMiddleware($container))
        ->setName('auth.login');
    $this->get('/signup', 'HomeController:signup')
        ->add(new \App\Middlewares\GuestMiddleware($container))
        ->setName('auth.signup');
    $this->get('/logout', function ($req, $res, $args) {
        session_destroy();
        unset($_SESSION);

        return $res->withHeader('Location', $this->container->router->pathFor('index'));
    })->add(new \App\Middlewares\UserMiddleware($container))
      ->setName('auth.logout');

    $this->post('/login', 'HomeController:login')
        ->add(new \App\Middlewares\LoginMiddleware($container))
        ->setName('auth.login.post');
    $this->post('/signup', 'HomeController:signup')
        ->add(/*new \App\Middlewares\SignUpMiddleware($container)*/)
        ->setName('auth.signup.post');
});
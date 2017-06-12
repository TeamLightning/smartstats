<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2017 xXAlphaManXx
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

use App\Controllers\HomeController;
use App\Controllers\UserController;

$app->get('/', HomeController::class . ':index')
    ->add(new \App\Middlewares\GuestMiddleware($container))
    ->setName('index');

$app->group('/auth', function () use ($container) {
    $this->get('/logout', function ($req, $res, $args) {
        session_destroy();
        unset($_SESSION);
        $_SESSION = null;

        return $res->withHeader('Location', '/');
    })->add(new \App\Middlewares\UserMiddleware($container))
        ->setName('auth.logout');
    $this->get('/login', HomeController::class . ':login')
        ->add(new \App\Middlewares\GuestMiddleware($container))
        ->setName('auth.login');
    $this->get('/signup', HomeController::class . ':signup')
        ->add(new \App\Middlewares\GuestMiddleware($container))
        ->setName('auth.signup');

    $this->post('/login', HomeController::class . ':getlogin')
        ->add(new \App\Middlewares\GuestMiddleware($container))
        ->setName('auth.login.post');
    $this->post('/signup', HomeController::class . ':getsignup')
        ->add(new \App\Middlewares\GuestMiddleware($container))
        ->setName('auth.signup.post');
});

$app->group('/user', function () {
    $this->get('/home', UserController::class . ':home')
        ->setName('user.home');
    $this->get('/show', UserController::class . ':show')
        ->setName('user.show');
    $this->get('/create', UserController::class . ':showCreate')
        ->setName('user.create');
    $this->get('/contact', UserController::class . ':contact')
        ->setName('user.contact');
    $this->get('/delete/{id}', UserController::class . ':delete')
        ->setName('user.delete');

    $this->post('/create', UserController::class . ':create')
        ->setName('user.create.post');
    $this->post('/contact', UserController::class . ':getContact')
        ->setName('user.contact.post');
})->add(new \App\Middlewares\UserMiddleware($container));

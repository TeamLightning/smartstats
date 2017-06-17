<?php

namespace App\Middlewares;


class Middleware {

    /**
     * @var \Slim\Container $container
     */
    protected $container;

    /**
     * @var \Delight\Auth\Auth $auth
     */
    protected $auth;

    public function __construct($container)
    {
        $this->container = $container;
        $this->auth      = $container->auth;
    }
}
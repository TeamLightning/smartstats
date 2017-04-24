<?php

namespace App\Authenticator;


class Auth
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }
}
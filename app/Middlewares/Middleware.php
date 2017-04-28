<?php

namespace App\Middlewares;

class Middleware
{
    /**
     * @var \Slim\Container $container
     */
    protected $container;

    /**
     * Middleware constructor.
     * @param \Slim\Container $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @return bool
     */
    protected function loggedIn()
    {
        if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === TRUE) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @return bool
     */
    protected function cookieSet()
    {
        if (isset($_COOKIE['cookie'])) {
            return true;
        }

        return false;
    }
}
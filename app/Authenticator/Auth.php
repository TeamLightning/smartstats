<?php

namespace App\Authenticator;


class Auth
{
    /**
     * @var \Slim\Container $container
     */
    protected $container;

    /**
     * Auth constructor.
     * @param \Slim\Container $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }


    /**
     * @param \Psr\Http\Message\ResponseInterface $res
     * @param string $template
     * @param array $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function view($res, $template, array $args = [])
    {
        if(count($args)) {
            return $this->container->view->render($res, $template . '.twig', $args);
        } else {
            return $this->container->view->render($res, $template . '.twig');
        }
    }

    /**
     * @return \Medoo\Medoo
     */
    protected function db()
    {
        return $this->container->db;
    }

    /**
     * @return bool
     */
    protected function cookieSet()
    {
        if (isset($_COOKIE['cookie']) && $_COOKIE['cookie'] === true) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function loggedIn()
    {
        if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === TRUE) {
            return true;
        }

        return false;
    }
}
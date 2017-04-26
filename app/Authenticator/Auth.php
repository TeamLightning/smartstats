<?php

namespace App\Authenticator;


use Medoo\Medoo;

class Auth
{
    /**
     * @var
     */
    protected $container;

    /**
     * Auth constructor.
     * @param $container
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
}
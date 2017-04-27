<?php

namespace App\Controllers;

class Controller
{
    /**
     * @var \Slim\Container
     */
    protected $container;

    /**
     * Controller constructor.
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param $property
     * @return mixed|null
     */
    public function __get($property)
    {
        if ($this->container->{$property}) {
            return $this->container->{$property};
        } else {
            return null;
        }
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $res
     * @param string $template
     * @param array $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function view($res, $template, $args = [])
    {
        return $this->container->view->render($res, $template . '.twig', $args);
    }
}

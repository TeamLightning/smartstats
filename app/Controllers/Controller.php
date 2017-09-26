<?php

namespace App\Controllers;

class Controller
{
    /**
     * @var \Slim\Container
     */
    protected $container;

    /**
     * @var \Illuminate\Database\Capsule\Manager
     */
    protected $db;

    /**
     * Controller constructor.
     *
     * @param \Slim\Container $container
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->db        = $container->db;
    }

    public function __get($property)
    {
        if ($this->container->{$property}) {
            return $this->container->{$property};
        } else {
            return;
        }
    }

    /**
     * The view method returning TwigRenderer's Class.
     *
     * @param string                                                  $template
     * @param array                                                   $args
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $res
     *
     * @return \Psr\Http\Message\ResponseInterface|\Slim\Http\Response
     */
    public function view($template, $args = [], $res)
    {
        return $this->container->view->render($res, $template.'.twig', $args);
    }

}

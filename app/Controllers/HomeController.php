<?php

namespace App\Controllers;

class HomeController extends Controller
{
    /**
     * The main controller 'index'.
     *
     * @param \Psr\Http\Message\RequestInterface|\Slim\Http\Request   $req
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $res
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $args
     *
     * @return \Psr\Http\Message\ResponseInterface|\Slim\Http\Response
     */
    public function index($req, $res, $args)
    {
        return $this->view('home', [], $res);
    }
}

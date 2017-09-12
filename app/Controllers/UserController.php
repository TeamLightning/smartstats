<?php

namespace App\Controllers;


class UserController extends Controller {

    /**
     * Returns the home page of the user to the browser
     *
     * @param \Psr\Http\Message\RequestInterface|\Slim\Http\Request   $req
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $res
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $args
     *
     * @return \Psr\Http\Message\ResponseInterface|\Slim\Http\Response
     */
    public function home($req, $res, $args)
    {
        return $this->view('user/home', [], $res);
    }

    /**
     * Returns the trash page of the user to the browser
     *
     * @param \Psr\Http\Message\RequestInterface|\Slim\Http\Request   $req
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $res
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $args
     *
     * @return \Psr\Http\Message\ResponseInterface|\Slim\Http\Response
     */
    public function trash ($req, $res, $args)
    {
        return $this->view('user/trash', [], $res);
    }

    /**
     * Returns the Offline servers page of the user to the browser
     *
     * @param \Psr\Http\Message\RequestInterface|\Slim\Http\Request   $req
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $res
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $args
     *
     * @return \Psr\Http\Message\ResponseInterface|\Slim\Http\Response
     */
    public function offlineServers ($req, $res, $args)
    {
        return $this->view('user/offlineServer', [], $res);
    }

    /**
     * Returns the Online Servers page of the user to the browser
     *
     * @param \Psr\Http\Message\RequestInterface|\Slim\Http\Request   $req
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $res
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $args
     *
     * @return \Psr\Http\Message\ResponseInterface|\Slim\Http\Response
     */
    public function onlineServers ($req, $res, $args)
    {
        return $this->view('user/onlineServer', [], $res);
    }
}
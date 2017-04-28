<?php

namespace App\Controllers;

class HomeController extends Controller
{
    /**
     * @param \Psr\Http\Message\RequestInterface $req
     * @param \Psr\Http\Message\ResponseInterface $res
     * @param \Psr\Http\Message\ResponseInterface $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index($req, $res, $args)
    {
        return $this->view($res, 'home');
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $req
     * @param \Psr\Http\Message\ResponseInterface $res
     * @param \Psr\Http\Message\ResponseInterface $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function login($req, $res, $args)
    {
        return $this->view($res, 'auth/login');
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $req
     * @param \Psr\Http\Message\ResponseInterface $res
     * @param \Psr\Http\Message\ResponseInterface $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function signup($req, $res, $args)
    {
        return $this->view($res, 'auth/signup');
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $req
     * @param \Psr\Http\Message\ResponseInterface $res
     * @param \Psr\Http\Message\ResponseInterface $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getlogin($req, $res, $args)
    {
        return $this->LoginHandler->login($req, $res, $args);
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $req
     * @param \Psr\Http\Message\ResponseInterface $res
     * @param \Psr\Http\Message\ResponseInterface $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getsignup($req, $res, $args)
    {
        return $this->SignUpHandler->auth($req, $res, $args);
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $req
     * @param \Psr\Http\Message\ResponseInterface $res
     * @param \Psr\Http\Message\ResponseInterface $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function wake($req, $res, $args)
    {
        return $this->view($res, 'auth/cookie', [
            'last' => date('d-M-Y H:i:s', $_COOKIE['last-visit']),
            'username' => $_COOKIE['username'],
        ]);
    }
}

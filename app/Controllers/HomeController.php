<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2017 xXAlphaManXx
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace App\Controllers;

class HomeController extends Controller
{
    /**
     * @param \Psr\Http\Message\RequestInterface|\Slim\Http\Request $req
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $res
     * @param \Psr\Http\Message\ResponseInterface $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index($req, $res, $args)
    {
        return $this->view($res, 'home');
    }

    /**
     * @param \Psr\Http\Message\RequestInterface|\Slim\Http\Request $req
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $res
     * @param \Psr\Http\Message\ResponseInterface $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function login($req, $res, $args)
    {
        return $this->view($res, 'auth/login');
    }

    /**
     * @param \Psr\Http\Message\RequestInterface|\Slim\Http\Request $req
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $res
     * @param \Psr\Http\Message\ResponseInterface $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function signup($req, $res, $args)
    {
        return $this->view($res, 'auth/signup');
    }

    /**
     * @param \Psr\Http\Message\RequestInterface|\Slim\Http\Request $req
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $res
     * @param \Psr\Http\Message\ResponseInterface $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getlogin($req, $res, $args)
    {
        return $this->LoginHandler->login($req, $res, $args);
    }

    /**
     * @param \Psr\Http\Message\RequestInterface|\Slim\Http\Request $req
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $res
     * @param \Psr\Http\Message\ResponseInterface $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getsignup($req, $res, $args)
    {
        return $this->SignUpHandler->auth($req, $res, $args);
    }

    /**
     * @param \Psr\Http\Message\RequestInterface|\Slim\Http\Request $req
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $res
     * @param \Psr\Http\Message\ResponseInterface $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function wake($req, $res, $args)
    {
        return $this->view($res, 'auth/cookie', [
            'username' => $_COOKIE['username'],
        ]);
    }

    /**
     * @param \Psr\Http\Message\RequestInterface|\Slim\Http\Request $req
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $res
     * @param \Psr\Http\Message\ResponseInterface $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function new($req, $res, $args)
    {
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                setcookie($name, '', time()-1000);
                setcookie($name, '', time()-1000, '/');
            }

            return $res->withHeader('Location', $this->container->router->pathFor('index'));
        }

        // Will not happen, but still, My IDE irritates me with error message
        return $res->withHeader('Location', $this->container->router->pathFor('index'));
    }
}

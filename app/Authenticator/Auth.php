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
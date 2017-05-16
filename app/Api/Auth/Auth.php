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

/**
 * Created by PhpStorm.
 * User: alphaman
 * Date: 5/16/17
 * Time: 10:40 AM
 */

namespace app\Api\Auth;


class Auth
{
    /**
     * @var \Slim\Container
     */
    protected $container;

    /**
     * @var \Medoo\Medoo
     */
    protected $db;

    public function __construct($container)
    {
        $this->container = $container;
        $this->db = $this->container->db;
    }

    public function cookieSet()
    {
        if (isset($_COOKIE['cookie']) && $_COOKIE['cookie'] === true) {
            return true;
        }

        return false;
    }

    /**
     * @param array $data
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $res
     *
     * @return \Psr\Http\Message\ResponseInterface|\Slim\Http\Response
     */
    public function sendJson(array $data, $res)
    {
        return $res->withJson($data);
    }
}
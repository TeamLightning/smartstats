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

class Controller
{
    /**
     * @var \Slim\Container
     */
    protected $container;

    /**
     * @var \Medoo\Medoo $db
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
        $this->db = $container->db;
    }

    /**
     * @param $property
     *
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
     * @param \Psr\Http\Message\ResponseInterface|\SLim\Http\Response $res
     * @param string                                                  $template
     * @param array                                                   $args
     *
     * @return \Psr\Http\Message\ResponseInterface|\SLim\Http\Response
     */
    public function view($res, $template, $args = [])
    {
        return $this->container->view->render($res, $template . '.twig', $args);
    }
}

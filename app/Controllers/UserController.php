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

use Violin\Violin as v;

class UserController extends Controller
{
    /**
     * @param \Psr\Http\Message\RequestInterface $req
     * @param \Psr\Http\Message\ResponseInterface $res
     * @param \Psr\Http\Message\ResponseInterface $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function home($req, $res, $args)
    {
        return $this->view($res, 'user/home');
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $req
     * @param \Psr\Http\Message\ResponseInterface $res
     * @param \Psr\Http\Message\ResponseInterface $args
     *
     * @return \Psr\Http\Message\ResponseInterface|\Medoo\Medoo
     */
    public function create($req, $res, $args)
    {
        $data = $req->getParsedBody();
        $v = new v;

        $v->validate([
            'ip|IPAddress' => [$data['ip'], 'ip|required|min(10)|max(20)'],
            'port|Port' => [$data['port'], 'required|min(2)|max(5)'],
            'name|Server Name' => [$data['name'], 'required|alnumDash|min(5)|max(40)']
        ]);

        if ($v->fails()) {
            return $this->view($res, 'user/create', [
                'validation' => $v->errors()
            ]);
        } else {
            $this->db->insert('servers', [
                'user' => $_SESSION['user_id'],
                'name' => $data['name'],
                'port' => $data['port'],
                'ip' => $data['ip']
            ]);

            return $this->view($res, 'user/create', [
                'message' => 'Added server'
            ]);
        }
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $req
     * @param \Psr\Http\Message\ResponseInterface $res
     * @param \Psr\Http\Message\ResponseInterface $args
     *
     * @return \Psr\Http\Message\ResponseInterface|\Medoo\Medoo
     */
    public function show($req, $res, $args)
    {
        $data = $this->db->select('servers', '*', [
            'user' => $_SESSION['user_id']
        ]);

        if (count($data) == 0) {
            return $this->view($res, 'user/show', [
                'error' => 'Oops, you have no server configured in the database'
            ]);
        }

        foreach ($data as $row) {
            $status = @fsockopen($row['ip'], $data['port'], $errno, $errstr, 0.2);

            if ($status) {
                return $this->view($res, 'user/show', [
                    'name' => $row['name'],
                    'ip' => $row['ip'],
                    'port' => $row['port'],
                    'status' => '<i class="fa fa-signal text-green"></i>&nbsp;Online',
                ]);
            } else {
                return $this->view($res, 'user/show', [
                    'name' => $row['name'],
                    'ip' => $row['ip'],
                    'port' => $row['port'],
                    'status' => '<i class="fa fa-signal text-red"></i>&nbsp;Offline',
                ]);
            }
        }

        // Will not happen. But I want to stop my IDE saying `return statement is missing`
        return $this->view($res, 'user/show', [
            'error' => 'Something really bad had happened'
        ]);
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $req
     * @param \Psr\Http\Message\ResponseInterface $res
     * @param \Psr\Http\Message\ResponseInterface $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showCreate($req, $res, $args)
    {
        return $this->view($res, 'user/create', []);
    }
}
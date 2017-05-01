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
        return $this->view($res, 'user/home', [
            'username' => $_SESSION['username'],
            'created_at' => $_SESSION['created_at'],
            'account' => ($_SESSION['account'] === 1) ? 'Admin user' : 'Free user',
            'c1' => 'active',
        ]);
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
        $check = $this->container->db->select('servers', 'id', ['user' => $_SESSION['user_id']]);

        if (count($check) == 1) {
            return $this->view($res, 'user/create', [
                'error' => 'You\'ve already created a server',
                'username' => $_SESSION['username'],
                'created_at' => $_SESSION['created_at'],
                'account' => ($_SESSION['account'] === 1) ? 'Admin user' : 'Free user',
                'c3' => 'active'
            ]);
        }

        $data = $req->getParsedBody();
        $v = new v;

        $v->validate([
            'ip|IPAddress' => [$data['ip'], 'ip|required'],
            'port|Port' => [$data['port'], 'required|min(2)|max(5)'],
            'name|Server Name' => [$data['name'], 'required|min(5)|max(40)']
        ]);

        if ($v->fails()) {
            return $this->view($res, 'user/create', [
                'validation' => $v->errors(),
                'username' => $_SESSION['username'],
                'created_at' => $_SESSION['created_at'],
                'account' => ($_SESSION['account'] === 1) ? 'Admin user' : 'Free user',
                'c3' => 'active'
            ]);
        } else {
            $this->db->insert('servers', [
                'user' => $_SESSION['user_id'],
                'name' => $data['name'],
                'port' => $data['port'],
                'ip' => $data['ip']
            ]);

            return $this->view($res, 'user/create', [
                'message' => 'Added server',
                'username' => $_SESSION['username'],
                'created_at' => $_SESSION['created_at'],
                'account' => ($_SESSION['account'] === 1) ? 'Admin user' : 'Free user',
                'c3' => 'active'
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
                'error' => 'Oops, you have no server configured in the database. Create a server by going to <a href="/user/create">create server page</a>',
                'username' => $_SESSION['username'],
                'created_at' => $_SESSION['created_at'],
                'account' => ($_SESSION['account'] === 1) ? 'Admin user' : 'Free user',
                'c2' => 'active'
            ]);
        }

        foreach ($data as $row) {
            if (@fsockopen($row['ip'], $row['port'], $errno, $errstr, 0.2)) {
                return $this->view($res, 'user/show', [
                    'name' => $row['name'],
                    'ip' => $row['ip'],
                    'port' => $row['port'],
                    'online' => 'Online',
                    'username' => $_SESSION['username'],
                    'created_at' => $_SESSION['created_at'],
                    'account' => ($_SESSION['account'] === 1) ? 'Admin user' : 'Free user',
                    'c2' => 'active',
                    'id' => $row['id'],
                ]);
            } else {
                return $this->view($res, 'user/show', [
                    'name' => $row['name'],
                    'ip' => $row['ip'],
                    'port' => $row['port'],
                    'offline' => 'Offline',
                    'username' => $_SESSION['username'],
                    'created_at' => $_SESSION['created_at'],
                    'account' => ($_SESSION['account'] === 1) ? 'Admin user' : 'Free user',
                    'id' => $row['id'],
                    'c2' => 'active'
                ]);
            }
        }

        // Will not happen. But I want to stop my IDE saying `return statement is missing`
        return $this->view($res, 'user/show', [
            'error' => 'Something really bad had happened',
            'username' => $_SESSION['username'],
            'created_at' => $_SESSION['created_at'],
            'account' => ($_SESSION['account'] === 1) ? 'Admin user' : 'Free user',
            'c2' => 'active'
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
        return $this->view($res, 'user/create', [
            'username' => $_SESSION['username'],
            'created_at' => $_SESSION['created_at'],
            'account' => ($_SESSION['account'] === 1) ? 'Admin user' : 'Free user',
            'c3' => 'active'
        ]);
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $req
     * @param \Psr\Http\Message\ResponseInterface $res
     * @param \Psr\Http\Message\ResponseInterface $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function contact($req, $res, $args)
    {
        return $this->view($res, 'user/contact', [
            'username' => $_SESSION['username'],
            'created_at' => $_SESSION['created_at'],
            'account' => ($_SESSION['account'] === 1) ? 'Admin user' : 'Free user',
            'c4' => 'active'
        ]);
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $req
     * @param \Psr\Http\Message\ResponseInterface $res
     * @param \Psr\Http\Message\ResponseInterface $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function delete($req, $res, $args)
    {
        if ($_COOKIE['username'] === $_SESSION['username']) {
            $this->db->delete('servers', [
                'id' => htmlspecialchars($args['id'])
            ]);

            return $res->withHeader('Location', $this->container->router->pathFor('user.create'));
        }

        return $res->withHeader('Location', $this->container->router->pathFor('user.show'));
    }
}

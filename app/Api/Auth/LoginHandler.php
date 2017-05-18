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
 * Time: 10:38 AM
 */

namespace app\Api\Auth;


class LoginHandler extends Auth
{
    /**
     * @param \Psr\Http\Message\RequestInterface|\Slim\Http\Request $req
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $res
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $args
     *
     * @return \Psr\Http\Message\ResponseInterface|\Slim\Http\Response
     */
    public function login($req, $res, $args)
    {
        $data = $req->getParsedBody();
        $v = new \Violin\Violin();

        if($this->cookieSet()) {
            return $res->withHeader('Location', $this->container->router->pathFor('index'));
        }

        $v->validate([
            'username|Username' => [$data['username'], 'required|alnumDash|min(5)|max(20)'],
            'password|Password' => [$data['password'], 'required|alnumDash|min(%)|max(100)']
        ]);

        if($v->fails()) {
            return $this->sendJson($v->errors(), $res);
        }

        $result = $this->db->select('users', '*', ['username' => $data['username']]);

        if(count($result) == 0) {
            return $this->sendJson([
                'error' => 'Oops, the username you entered is not available'
            ], $res);
        }

        foreach ($result as $row) {
            if(password_verify($data['password'], $row['password'])) {
                $_SESSION['username']   = $row['username'];
                $_SESSION['user_id']    = $row['id'];
                $_SESSION['created_at'] = date('d-M-Y H:i:s', $row['created_at']);
                $_SESSION['loggedIn']   = TRUE;

                setcookie('created_at', date('d-M-Y H:i:s', $row['created_at']), time() * 2);
                setcookie('username', $row['username'], time() * 2, '/');
                setcookie('user_id', $row['id'], time() * 2, '/');
                setcookie('loggedIn', true, time() * 2, '/');
                setcookie('cookie', true, time() * 2, '/');

                if ($row['account'] === 1) {
                    $_SESSION['account'] = 'Pro User';
                    $_SESSION['type']    = 1;

                    setcookie('account', 'Pro User', time() * 2, '/');
                    setcookie('type', 1, time() * 2, '/');
                } elseif ($row['account'] === 2) {
                    $_SESSION['account'] = 'Admin User';
                    $_SESSION['type']    = 2;

                    setcookie('account', 'Admin User', time() * 2, '/');
                    setcookie('type', 2, time() * 2, '/');
                } else {
                    $_SESSION['account'] = 'Free User';
                    $_SESSION['type']    = 0;

                    setcookie('account', 'Free User', time() * 2, '/');
                    setcookie('type', 0, time() * 2, '/');
                }

                return $this->sendJson([
                    'success' => 'You\'ve successfully logged in',
                    'redirect' => true
                ], $res);
            } else {
                return $this->sendJson([
                    'error' => 'Oops, the password is wrong'
                ] ,$res);
            }
        }

        return $this->sendJson([
            'error' => 'Oops, something really bad happened with the system'
        ] ,$res);
    }
}
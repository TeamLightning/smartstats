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


class CookieHandler extends Auth
{
    /**
     * @param \Psr\Http\Message\RequestInterface $req
     * @param \Psr\Http\Message\ResponseInterface $res
     * @param \Psr\Http\Message\ResponseInterface $args
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function login($req, $res, $args)
    {
        // Security check. Will not happen because it is taken care by Middleware. But what if some one set cookie
        // manually?
        if ($this->loggedIn()) {
            return $res->withHeader('Location', $this->container->router->pathFor('user.home'));
        }

        $data = $req->getParsedBody();
        $v = new \Violin\Violin;

        $v->validate([
            'password|Password' => [$data['password'], 'required|alnumDash|min(6)|max(100)']
        ]);

        switch ($v->passes()) {
            case true:
                return $this->okay($res, $data);
                break;
            case false:
                return $this->view($res, 'auth/cookie', [
                    'validation' => $v->errors(),
                ]);
                break;
            default:
                return $this->view($res, 'auth/cookie', [
                    'error' => 'Something really bad had happened',
                ]);
        }
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $res
     * @param array $data
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function okay($res, $data)
    {
        $results = $this->db()->select('users', [
            'password'
        ], [
            'username' => $_COOKIE['username']
        ]);

        if (count($results) == 0) {
            return $this->view($res, 'auth/cookie', [
                'error' => 'Something really bad had happened',
            ]);
        }

        foreach ($results as $row) {
            if (password_verify($data['password'], $row['password'])) {
                $_SESSION['username'] = $_COOKIE['username'];
                $_SESSION['account'] = $_COOKIE['account'];
                $_SESSION['user_id'] = $_COOKIE['user_id'];
                $_SESSION['loggedIn'] = TRUE;

                return $res->withHeader('Location', $this->container->router->pathFor('user.home'));
            } else {
                return $this->view($res, 'auth/cookie', [
                    'error' => 'Password is wrong',
                ]);
            }
        }
    }
}
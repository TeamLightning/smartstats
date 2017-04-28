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

use Violin\Violin as v;


class LoginHandler extends Auth
{
    /**
     * @param \Psr\Http\Message\RequestInterface       $req
     * @param \Psr\Http\Message\ResponseInterface      $res
     * @param \Psr\Http\Message\ResponseInterface      $next
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function login($req, $res, $next)
    {
        $data = $req->getParsedBody();
        $v = new v;

        if ($this->cookieSet()) {
            return $res->withHeader('Location', $this->container->router->pathFor('auth.cookie'));
        }

        $v->validate([
            'username|Username' => [$data['username'], 'required|alnumDash|min(5)|max(20)'],
            'password|Password' => [$data['password'], 'required|alnumDash|min(6)|max(100)'],
        ]);

        if($v->fails()) {
            return $this->view($res, 'auth/login', [
                'validation' => $v->errors(),
            ]);
        } else {

            $result = $this->container->db->select('users', '*', [
                'username' => $data['username'],
            ]);

            switch (count($result)) {
                case 1:
                    $this->authenticate($data, $req, $res, $next);
                    break;
                case 0:
                    return $this->view($res, 'auth/login', [
                        'error' => 'Sorry, no account is associated with this username',
                    ]);
                    break;
                default:
                    return $this->view($res, 'auth/login', [
                        'error' => 'Something really bad had happened. But don\'t worry, your account is safe',
                    ]);
            }
        }
    }

    /**
     * @param \Psr\Http\Message\RequestInterface array $data
     * @param \Psr\Http\Message\RequestInterface       $req
     * @param \Psr\Http\Message\ResponseInterface      $res
     * @param \Psr\Http\Message\ResponseInterface      $next
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function authenticate($data, $req, $res, $next)
    {
        $results = $this->container->db->select('users', '*', ['username' => $data['username']]);

        foreach ($results as $result) {
            if (password_verify($data['password'], $result['password'])) {
                $_SESSION['username'] = $result['username'];
                $_SESSION['account'] = $result['account'];
                $_SESSION['user_id'] = $result['id'];
                $_SESSION['loggedIn'] = TRUE;

                setcookie('username', $result['username'], time() * 2, '/');
                setcookie('account', $result['account'], time() * 2, '/');
                setcookie('user_id', $result['id'], time() * 2, '/');
                setcookie('loggedIn', true, time() * 2, '/');
                setcookie('cookie', true, time() * 2, '/');

                // Temporary solution.
                // @todo: A more robust solution required in v: 1.0.2
                return $this->view($res, 'temp');
            } else {
                return $this->view($res, 'auth/login', [
                    'error' => 'Oops, the password is wrong. Why not give another try?',
                ]);
            }
        }

        //This will not happen. But, I wanted my IDE to un-mark this function as incomplete

        return $this->view($res, 'auth/login', [
            'error' => 'Oops, something really bad had happened. This actually should not occur.',
        ]);
    }
}

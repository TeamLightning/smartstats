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

class SignUpHandler extends Auth
{
    /**
     * @param \Psr\Http\Message\RequestInterface|\Slim\Http\Request $req
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $res
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $next
     *
     * @return \Psr\Http\Message\ResponseInterface|\Slim\Http\Response
     */
    public function auth($req, $res, $next)
    {
        $data = $req->getParsedBody();
        $v = new v;

        if($data['password'] !== $data['password_r']){
            return $this->view($res, 'auth/signup', [
                'error' => 'The passwords didn\'t match',
            ]);
        }

        $v->validate([
            'username|Username'          => [$data['username'], 'required|alnumDash|min(5)|max(20)'],
            'password|Password'          => [$data['password'], 'required|alnumDash|min(6)|max(100)'],
            'password_r|Password Repeat' => [$data['password_r'], 'required|alnumDash|min(5)|max(100)'],
        ]);

        if($v->fails()) {
            return $this->view($res, 'auth/signup', [
                'validation' => $v->errors(),
            ]);
        } else {
            $this->checkUsername($data, $req, $res, $next);
        }
    }

    /**
     * @param \Psr\Http\Message\RequestInterface|\Slim\Http\Request $data
     * @param \Psr\Http\Message\RequestInterface|\Slim\Http\Request $req
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $res
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $next
     *
     * @return \Psr\Http\Message\ResponseInterface|\Slim\Http\Response
     */
    private function checkUsername($data, $req, $res, $next)
    {
        $result = count($this->db->select('users', 'id', [
            'username' => $data['username']
        ]));

        switch ($result) {
            case 1:
                return $this->view($res, 'auth/signup', [
                    'error' => 'The username already exist in our database'
                ]);
                break;

            case 0:
                $this->signUp($data, $req, $res, $next);
                break;

            default:
                return $this->view($res, 'auth/signup', [
                    'error' => 'Oops, something really bad had happened in our system',
                ]);
        }
    }

    /**
     * @param \Psr\Http\Message\RequestInterface|\Slim\Http\Request $data
     * @param \Psr\Http\Message\RequestInterface|\Slim\Http\Request $req
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $res
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $next
     *
     * @return \Psr\Http\Message\ResponseInterface|\Slim\Http\Response
     */
    private function signUp($data, $req, $res, $next)
    {
        $this->db->insert('users', [
            'username' => htmlspecialchars($data['username']),
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'account' => 0,
            'created_at' => time(),
        ]);

        return $this->view($res, 'auth/login', [
            'message' => 'Account had been created successfully. Login now',
        ]);
    }
}

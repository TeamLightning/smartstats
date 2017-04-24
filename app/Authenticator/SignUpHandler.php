<?php

namespace App\Authenticator;

use Violin\Violin as v;

class SignUpHandler extends Auth
{
    public function auth($req, $res, $next)
    {
        if($this->loggedIn()) {
            return $res->withHeader('Location', $this->container->router->pathFor('user.home'));
        }

        $data = $req->getParsedBody();
        $v = new v;

        if($data['password'] !== $data['password_r']){
            return $this->container->view->render($res, 'auth/signup.twig', [
                'error' => 'The passwords didn\'t match',
            ]);
        }

        $v->validate([
            'username|Username'          => [$data['username'], 'required|alnumDash|min(5)|max(20)'],
            'password|Password'          => [$data['password'], 'required|alnumDash|min(6)|max(100)'],
            'password_r|Password Repeat' => [$data['password_r'], 'required|alnumDash|min(5)|max(100)'],
        ]);

        if($v->fails()) {
            return $this->container->view->render($res, 'auth/signup.twig', [
                'validation' => $v->errors(),
            ]);
        } else {
            $this->checkUsername($data, $req, $res, $next);
        }
    }

    /**
     * @param array                                     $data   Gets the POST data from the server
     * @param \Psr\Http\Message\ServerRequestInterface  $req    Binds the PSR-7 request object.
     * @param \Psr\Http\Message\ResponseInterface       $res    Binds the PSR-7 response object.
     * @param array                                     $next   Gets all the parameters passed.
     *
     * @return \Psr\Http\Message\ResponseInterface|mixed
     */
    private function checkUsername($data, $req, $res, $next)
    {
        $result = count($this->container->db->select('users', '*', ['username' => $data['username']]));

        switch ($result) {
            case 1:
                return $this->container->view->render($res, 'auth/signup.twig', [
                    'error' => 'The username already exist in our database'
                ]);
                break;

            case 0:
                $this->signUp($data, $req, $res, $next);
                break;

            default:
                return $this->container->view->render($res, 'auth/signup.twig', [
                    'error' => 'Oops, something really bad had happened in our system',
                ]);
        }
    }

    /**
     * @param array                                     $data   Gets the POST data from the server
     * @param \Psr\Http\Message\ServerRequestInterface  $req    Binds the PSR-7 request object.
     * @param \Psr\Http\Message\ResponseInterface       $res    Binds the PSR-7 response object.
     * @param array                                     $next   Gets all the parameters passed.
     *
     * @return \Psr\Http\Message\ResponseInterface|mixed
     */
    private function signUp($data, $req, $res, $next)
    {
        $this->container->db->insert('users', [
            'username' => htmlspecialchars($data['username']),
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'account'  => 0,
        ]);

        return $this->container->view->render($res, 'auth/login.twig', [
            'message' => 'Account had been created successfully. Login now',
        ]);
    }

    private function loggedIn()
    {
        if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === TRUE) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}

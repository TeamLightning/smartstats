<?php

namespace App\Authenticator;

use Violin\Violin as v;


class LoginHandler extends Auth
{
    public function login($req, $res, $next)
    {
        if($this->loggedIn()) {
            return $res->withHeader('Location', $this->container->router->pathFor('user.home'));
        }

        $data = $req->getParsedBody();
        $v = new v;

        $v->validate([
            'username|Username' => [$data['username'], 'required|alnumDash|min(5)|max(20)'],
            'password|Password' => [$data['password'], 'required|alnumDash|min(6)|max(100)'],
        ]);

        if($v->fails()) {
            return $this->container->view->render($res, 'auth/login.twig', [
                'validation' => $v->errors(),
            ]);
        } else {

            $result = $this->container->db->select('users', '*', [
                'username' => $data['username'],
            ]);

            switch (count($result)) {
                case 1:
                    $this->authenticate($result, $data, $req, $res, $next);
                    break;
                case 0:
                    return $this->container->view->render($res, 'auth/login.twig', [
                        'error' => 'Sorry, no account is associated with this username',
                    ]);
                    break;
                default:
                    return $this->container->view->render($res, 'auth/login.twig', [
                        'error' => 'Something really bad had happened. But don\'t worry, your account is safe',
                    ]);
            }
        }
    }

    /**
     * @param \Medoo\Medoo                             $results
     * @param \Psr\Http\Message\RequestInterface array $data
     * @param \Psr\Http\Message\RequestInterface       $req
     * @param \Psr\Http\Message\ResponseInterface      $res
     * @param \Psr\Http\Message\ResponseInterface      $next
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function authenticate($results, $data, $req, $res, $next)
    {
        foreach ($results as $result):
            if(password_verify($data['password'], $result->password)) {
                $_SESSION['username'] = $result->username;
                $_SESSION['account']  = $result->account;
                $_SESSION['loggedIn'] = TRUE;

                return $res->withHeader('Location', $this->container->router->pathFor('user.home'));
            } else {
                return $this->container->view->render($res, 'auth/login.twig', [
                    'error' => 'Oops, the password is wrong. Why not give another try?',
                ]);
            }
        endforeach;

        //This will not happen. But, I wanted my IDE to un-mark this function as incomplete

        return $this->container->view->render($res, 'auth/login.twig', [
            'error' => 'Oops, something really bad had happened. This actually should not occur.',
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

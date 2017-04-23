<?php

namespace App\Middlewares;


class GuestMiddleware extends Middleware
{
    public function __invoke($req, $res , $next)
    {
        if($this->loggedIn()) {
            return $res->withHeader('Location', $this->container->router->pathFor('user.home'));
        } else {
            $res = $next($req, $res);
            return $res;
        }
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
<?php

namespace App\Middlewares;


class UserMiddleware extends Middleware
{
    public function __invoke($req, $res, $next)
    {
        if($this->loggedIn()) {
            $res = $next($req, $res);
            return $res;
        } else {
            return $res->withHeader('Location', $this->container->router->pathFor('index'));
        }
    }

    private function loggedIn()
    {
        if(isset($_SESSION['loggedIn'])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
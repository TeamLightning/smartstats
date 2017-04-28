<?php

namespace App\Middlewares;


class UserMiddleware extends Middleware
{
    public function __invoke(\Psr\Http\Message\RequestInterface $req,
                             \Psr\Http\Message\ResponseInterface $res, $next)
    {
        if($this->loggedIn()) {
            $res = $next($req, $res);
            return $res;
        } else {
            return $res->withHeader('Location', $this->container->router->pathFor('index'));
        }
    }
}
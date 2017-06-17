<?php

namespace App\Middlewares;


class GuestMiddleware extends Middleware {

    public function __invoke($req, $res, $next)
    {
        if ($this->auth->isLoggedIn()) {
            return $res->withRedirect($this->container->router->pathFor('user.home'));
        }

        $res = $next($req, $res);

        return $res;
    }
}
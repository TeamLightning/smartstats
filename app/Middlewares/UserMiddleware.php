<?php

namespace App\Middlewares;


class UserMiddleware extends Middleware {

    public function __invoke($req, $res, $next)
    {
        if ($this->auth->isLoggedIn()) {

            $res = $next($req, $res);

            return $res;
        }

        return $res->withRedirect($this->container->router->pathFor('user.home'));
    }
}
<?php

namespace App\Controllers;

class HomeController extends Controller
{
    /**
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function index($req, $res, $args)
    {
        return $this->view->render($res, 'home.twig');
    }

    /**
     * @param $req
     * @param $res
     * @param $args
     */
    public function login($req, $res, $args)
    {
        return $this->view->render($res, 'auth/login.twig');
    }

    /**
     * @param $req
     * @param $res
     * @param $args
     */
    public function signup($req, $res, $args)
    {
        return $this->view->render($res, 'auth/signup.twig');
    }
}

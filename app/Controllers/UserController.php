<?php

namespace App\Controllers;


class UserController extends Controller {

    public function home($req, $res, $args)
    {
        return $this->view('user/home', [], $res);
    }
}
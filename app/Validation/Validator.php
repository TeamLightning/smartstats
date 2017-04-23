<?php

namespace App\Validation;

use Violin\Violin as v;

class Validator
{
    /**
     * @var $errors
     */
    protected $errors;


    /**
     * @param \Psr\Http\Message\ServerRequestInterface $req
     * @return bool                                    $v->passes()
     */
    public function validate($req)
    {
        $v = new v;

        $data = $req->getParsedBody();

        $v->validate([
            'username|Username' => [$data['username'], 'required|alnumDash|between(5, 10)'],
            'password|Password' => [$data['password'], 'required|alnumDash|between(5, 100)'],
        ]);

        if(!$v->passes()) {
            $this->errors = $v->errors();
        }

        return $v->passes();
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
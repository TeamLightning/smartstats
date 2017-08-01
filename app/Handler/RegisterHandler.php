<?php

namespace App\Handler;


use App\Models\Info;
use Delight\Auth\Auth;
use App\Exceptions\InformationSavingError;

class RegisterHandler {

    /**
     * @param \Delight\Auth\Auth $auth
     *
     * @return bool
     * @throws \App\Exceptions\InformationSavingError
     */
    public function registerWithInformation (Auth $auth)
    {
        $info = new Info();

        $info->slots = 0;
        $info->user = $auth->getUserId();
        $info->type = 0;
        $info->max = 5;

        if ($info->save()) {
            return TRUE;
        }

        throw new InformationSavingError();
    }
}

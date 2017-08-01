<?php

namespace App\Handler;

use App\Models\User;
use Delight\Auth\Auth;

class UserInfoHandler {

    /**
     * UserInfoHandler constructor.
     *
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        return $this->setType($auth);
    }

    /**
     * @param \Delight\Auth\Auth $auth
     *
     * @return int
     */
    private function setType (Auth $auth)
    {
        $user = User::where('email', $auth->getEmail())->get();

        foreach ($user as $info) {

            switch ($info->type):

                // Free account
                case 0:
                    return $_SESSION['type'] = 0;
                    break;

                // Silver account
                case 1:
                    return $_SESSION['type'] = 1;
                    break;

                // Gold account
                case 2:
                    return $_SESSION['type'] = 2;
                    break;

                // Diamond account
                case 3:
                    return $_SESSION['type'] = 3;
                    break;

                // Admin account
                case 4:
                    return $_SESSION['type'] = 4;
                    break;

                // IMPOSSIBLE, BUT STILL
                default:
                    return $_SESSION['type'] = 0;
            endswitch;
        }

        // IMPOSSIBLE. But the IDE is killing me with errors
        return $_SESSION['type'] = 0;
    }
}
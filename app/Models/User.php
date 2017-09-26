<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function servers ()
    {
        return $this->hasMany(\App\Models\Server::class);
    }

    public function information ()
    {
        return $this->hasMany(\App\Models\Info::class);
    }

}
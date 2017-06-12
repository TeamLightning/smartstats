<?php
/**
 * Created by PhpStorm.
 * User: karan
 * Date: 6/12/2017
 * Time: 8:36 PM
 */

namespace App\Controllers;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class InstallController extends Controller
{
    public function start($req, $res, $args)
    {
        try {
            Capsule::schema()->create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('email')->unique();
                $table->string('password', 500);
                $table->integer('account')->default(0);
                $table->timestamps();
            });

            Capsule::schema()->create('servers', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user');
                $table->string('name');
                $table->integer('port');
                $table->ipAddress('ip');
                $table->timestamps();
            });

            return $this->view($res, 'install');
        } catch (\Exception $e) {
            return $this->view($res, 'install', $e->getMessage());
        }
    }
}
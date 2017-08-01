<?php

namespace App\Handler;

use App\Models\Info;
use App\Models\Server;

class ServerHandler extends Handler {

    /**
     * @param $name
     * @param $port
     * @param $ipAddress
     *
     * @return bool
     */
    public function addServer ($name, $port, $ipAddress)
    {
        $info   = Info::where('user', $this->auth->getUserId())->get();
        $server = new Server();

        $server->ipAddress = $ipAddress;
        $server->name      = $name;
        $server->port      = $port;
        $server->type      = 0;
        $server->user      = $this->auth->getUserId();

        if ($server->save()) {
            $info->offline_count = $info->offline_count + 1;

            if ($info->save()) {
                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * @param int $id ID of the server to be deleted
     */
    public function deleteServer ($id)
    {
        Server::where('id', $id)->delete();

        $info = Info::where('user', $this->auth->getUserId());
        $slot = $info->slots;

        $info->slots = $slot - 1;
        $info->save();
    }
}

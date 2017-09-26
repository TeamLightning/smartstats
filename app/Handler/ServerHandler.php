<?php

namespace App\Handler;

use App\Models\Info;
use App\Models\Server;

class ServerHandler extends Handler {

    /**
     * Status of deleting server
     *
     * @var bool
     */
    private $status = false;

    /**
     * Add server to the database
     *
     * @param string $name       Name of the server
     * @param int    $port       Port of the server
     * @param string $ipAddress  IP of the server
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
     * Delete the server of the user and increase the slot by one
     *
     * @param int $id ID of the server to be deleted
     *
     * @return bool
     */
    public function deleteServer ($id)
    {
        if (Server::where('id', $id)->delete()) {
            $this->status = true;
        } else { $this->status = false; }

        $info = Info::where('user', $this->auth->getUserId());
        $slot = $info->slots;

        $info->slots = $slot - 1;

        if ($info->save()) {
            $this->status = true;
        } else { $this->status = false; }

        return $this->status;
    }

}

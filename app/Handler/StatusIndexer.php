<?php

namespace App\Handler;

use App\Models\Info;
use App\Models\User;
use App\Models\Server;

class StatusIndexer {

    /**
     * @var \Slim\Container $container
     */
    protected $container;

    /**
     * @var \PHPMailer $mail
     */
    protected $mail;

    /**
     * StatusIndexer constructor.
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->mail      = new \PHPMailer();
    }

    public function indexServerFree()
    {
        Server::where('type', 0)->chunk(50, function ($servers)
        {
            foreach ($servers as $server) {
                $status = @fsockopen($server->ipAddress, $server->port, $errno, $errstr, 0.5);
                $info = Info::where('user', $server->user)->get();
                $user = User::where('id', $server->user)->get();

                if ($status) {

                    Server::where('id', $server->id)->update(['status' => 1]);
                } else {

                    Server::where('id', $server->id)->update(['status' => 0]);

                    $info->offline_count = $info->offline_count + 1;
                    $info->save();

                    $this->sendMail($user->email, $server->name, $server->ipAddress);
                }
            }
        });
    }

    public function indexServerSilver()
    {
        Server::where('type', 1)->chunk(50, function ($servers)
        {
            foreach ($servers as $server) {
                $status = @fsockopen($server->ipAddress, $server->port, $errno, $errstr, 0.5);
                $info = Info::where('user', $server->user)->get();
                $user = User::where('id', $server->user)->get();

                if ($status) {

                    Server::where('id', $server->id)->update(['status' => 1]);
                } else {

                    Server::where('id', $server->id)->update(['status' => 0]);

                    $info->offline_count = $info->offline_count + 1;
                    $info->save();

                    $this->sendMail($user->email, $server->name, $server->ipAddress);
                }
            }
        });
    }

    public function indexServerGold()
    {
        Server::where('type', 2)->chunk(50, function ($servers)
        {
            foreach ($servers as $server) {
                $status = @fsockopen($server->ipAddress, $server->port, $errno, $errstr, 0.5);
                $info = Info::where('user', $server->user)->get();
                $user = User::where('id', $server->user)->get();

                if ($status) {

                    Server::where('id', $server->id)->update(['status' => 1]);
                } else {

                    Server::where('id', $server->id)->update(['status' => 0]);

                    $info->offline_count = $info->offline_count + 1;
                    $info->save();

                    $this->sendMail($user->email, $server->name, $server->ipAddress);
                }
            }
        });
    }

    public function indexServerDiamond()
    {
        Server::where('type', 3)->chunk(50, function ($servers)
        {
            foreach ($servers as $server) {
                $status = @fsockopen($server->ipAddress, $server->port, $errno, $errstr, 0.5);
                $info = Info::where('user', $server->user)->get();
                $user = User::where('id', $server->user)->get();

                if ($status) {

                    Server::where('id', $server->id)->update(['status' => 1]);
                } else {

                    Server::where('id', $server->id)->update(['status' => 0]);

                    $info->offline_count = $info->offline_count + 1;
                    $info->save();

                    $this->sendMail($user->email, $server->name, $server->ipAddress);
                }
            }
        });
    }

    public function sendMail($to, $serverName, $serverIp)
    {
        // Sending logic
        $this->mail->addAddress($to);
        $this->mail->Subject('ATTENTION! YOUR SERVER IS OFFLINE - Smart Stats');
        $this->mail->Body("<b>ATTENTION Smart Stats user, your server named " . $serverName . " with IP" . $serverIp
                    . " was found <span style=\"color: red;\">offline</span>. Please take quick action. <br />"
                    . "<i>If you think this is an error from our side, then please reply to this E-Mail</i></b>");
        $this->mail->send();
    }
}
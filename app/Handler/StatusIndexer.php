<?php

namespace App\Handler;

class StatusIndexer {

    /**
     * @var \Slim\Container $container
     */
    protected $container;

    /**
     * @var \Illuminate\Database\Query\Builder $db
     */
    protected $db;

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
        $this->db        = $container->db->table('servers');
        $this->mail      = new \PHPMailer();
    }

    public function indexServerFree()
    {
        $this->db->select('*')->where('type', 0)->chunk(50, function ($servers)
        {
            foreach ($servers as $server) {
                $status = @fsockopen($server->ipAddress, $server->port, $errno, $errstr, 0.5);

                if ($status) {

                    $this->db->where(['id' => $server->id])->insert([
                        'status' => 1
                    ]);
                } else {

                    $this->db->where(['id' => $server->id])->insert([
                        'status' => 0
                    ]);
                    $this->sendMail($server->email, $server->name, $server->ipAddress);
                }
            }
        });
    }

    public function indexServerSilver()
    {
        $this->db->select('*')->where('type', 1)->chunk(50, function ($servers)
        {
            foreach ($servers as $server) {
                $status = @fsockopen($server->ipAddress, $server->port, $errno, $errstr, 0.5);

                if ($status) {

                    $this->db->where(['id' => $server->id])->insert([
                        'status' => 1
                    ]);
                } else {

                    $this->db->where(['id' => $server->id])->insert([
                        'status' => 0
                    ]);
                    $this->sendMail($server->email, $server->name, $server->ipAddress);
                }
            }
        });
    }

    public function indexServerGold()
    {
        $this->db->select('*')->where('type', 2)->chunk(50, function ($servers)
        {
            foreach ($servers as $server) {
                $status = @fsockopen($server->ipAddress, $server->port, $errno, $errstr, 0.5);

                if ($status) {

                    $this->db->where(['id' => $server->id])->insert([
                        'status' => 1
                    ]);
                } else {

                    $this->db->where(['id' => $server->id])->insert([
                        'status' => 0
                    ]);
                    $this->sendMail($server->email, $server->name, $server->ipAddress);
                }
            }
        });
    }

    public function indexServerDiamond()
    {
        $this->db->select('*')->where('type', 3)->chunk(50, function ($servers)
        {
            foreach ($servers as $server) {
                $status = @fsockopen($server->ipAddress, $server->port, $errno, $errstr, 0.5);

                if ($status) {

                    $this->db->where(['id' => $server->id])->insert([
                        'status' => 1
                    ]);
                } else {

                    $this->db->where(['id' => $server->id])->insert([
                        'status' => 0
                    ]);
                    $this->sendMail($server->email, $server->name, $server->ipAddress);
                }
            }
        });
    }

    public function sendMail($to, $serverName, $serverIp)
    {
        // Configuration
        $this->mail->isSMTP();
        $this->mail->Host = getenv('SMTP_HOST');
        $this->mail->SMTPAuth = true;
        $this->mail->Username = getenv('SMTP_USER');
        $this->mail->Password = getenv('SMTP_PASS');
        $this->mail->SMTPSecure = 'tls';
        $this->mail->Port = 587;
        $this->mail->isHTML(true);
        $this->mail->setFrom(getenv('SMTP_MAIL_FROM'), getenv('SMTP_MAIL_FRON_NAME'));
        $this->mail->addReplyTo(getenv('SMTP_MAIL_REPLY'), getenv('SMTP_MAIL_REPLY_NAME'));

        // Sending logic
        $this->mail->addAddress($to);
        $this->mail->Subject('ATTENTION! YOUR SERVER IS OFFLINE - Smart Stats');
        $this->mail->Body("<b>ATTENTION Smart Stats user, your server named " . $serverName . " with IP" . $serverIp
                    . " was found <span style=\"color: red;\">offline</span>. Please take quick action. <br />"
                    . "<i>If you think this is an error from our side, then please reply to this E-Mail</i></b>");
        $this->mail->send();
    }
}
<?php

namespace App\Handler;

class Handler {

    /**
     * @var \Slim\Container $container
     */
    protected $container;

    /**
     * @var \Delight\Auth\Auth $auth
     */
    protected $auth;

    /**
     * @var \PHPMailer $mail
     */
    protected $mail;

    /**
     * Handler constructor.
     *
     * @param $container
     */
    public function __construct ($container)
    {
        $this->container = $container;
        $this->auth      = $container->auth;
        $this->mail      = new \PHPMailer();
        $this->config();
    }

    private function config ()
    {
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
    }

}

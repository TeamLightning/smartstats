<?php

namespace App\Authenticator;

use Delight\Auth\AmbiguousUsernameException;
use Delight\Auth\TooManyRequestsException;
use Delight\Auth\TokenExpiredException;
use Delight\Auth\DuplicateUsernameException;
use Delight\Auth\UserAlreadyExistsException;
use Delight\Auth\InvalidPasswordException;
use Delight\Auth\InvalidSelectorTokenPairException;
use Delight\Auth\InvalidEmailException;
use Delight\Auth\EmailOrUsernameRequiredError;
use Delight\Auth\EmailNotVerifiedException;
use Delight\Auth\UnknownUsernameException;
use Delight\Auth\DatabaseError;

require __DIR__ . '/../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';

class AuthHandler
{
    /**
     * @var \Delight\Auth\Auth
     */
    protected $auth;

    /**
     * @var \PHPMailer
     */
    protected $mail;

    /**
     * @var \Slim\Container
     */
    protected $container;

    /**
     * @var
     */
    private $sent;

    public function __construct($container)
    {
        $container->config;
        $this->mail = new \PHPMailer;
        $this->auth = $container->auth;
        $this->container = $container;
        $this->config();
    }

    private function config()
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

    /**
     * @param \Psr\Http\Message\RequestInterface|\Slim\Http\Request   $req
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $res
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $args
     *
     * @return \Psr\Http\Message\ResponseInterface|\Slim\Http\Response
     */
    public function login($req, $res, $args)
    {
        $data = $req->getParsedBody();

        try {
            if(isset($data['remember']) && $data['remember'] === 'on') {
                $this->auth->loginWithUsername($data['username'], $data['password'], (int) (60 * 60 * 24 * 365.25));

                return $res->withRedirect($this->container->router->pathFor('user.home'));
            }

            $this->auth->loginWithUsername($data['username'], $data['password']);

            return $res->withRedirect($this->container->router->pathFor('user.home'));
        }
        catch (InvalidEmailException $e) {
            return $this->view('auth/login', [
                'message' => 'Invalid email, please try again with proper mail'
            ], $res);
        }
        catch (InvalidPasswordException $e) {
            return $this->view('auth/login', [
                'message' => 'Invalid password, please try again later'
            ], $res);
        }
        catch (EmailNotVerifiedException $e) {
            return $this->view('auth/login', [
                'message' => 'Email not yet verified. Verify email with the link sent to your link'
            ], $res);
        }
        catch (TooManyRequestsException $e) {
            return $this->view('auth/login', [
                'message' => 'Cool down! Clicking too much might hurt your fingers and PC keys'
            ], $res);
        }
        catch (UnknownUsernameException $e) {
            return $this->view('auth/login', [
                'message' => 'Username unknown. Please try again. Or create a new account'
            ], $res);
        }
        catch (AmbiguousUsernameException $e) {
            return $this->view('auth/login', [
                'message' => 'Username unknown. Please try again. Or create a new account'
            ], $res);
        }
        catch (EmailOrUsernameRequiredError $e) {
            return $this->view('auth/login', [
                'message' => 'Email or username is required. Try entering them.'
            ], $res);
        }
        catch (DatabaseError $e) {
            return $this->view('auth/login', [
                'message' => 'Unknown error with database. Problem reported to the developer'
            ], $res);
        }
    }

    /**
     * @param \Psr\Http\Message\RequestInterface|\Slim\Http\Request   $req
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $res
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $args
     *
     * @return \Psr\Http\Message\ResponseInterface|\Slim\Http\Response
     */
    public function verifyMail($req, $res, $args)
    {
        try {
            $this->auth->confirmEmail($args['selector'], $args['token']);

            return $this->view('auth/mailCheck', [
                'message' => 'Yuhu, you\'ve verified the mail, login now',
            ], $res);
        }
        catch (InvalidSelectorTokenPairException $e) {
            return $this->view('auth/mailCheck', [
                'message' => 'Oops, the link you followed does not seem to be valid',
            ], $res);
        }
        catch (TokenExpiredException $e) {
            return $this->view('auth/mailCheck', [
                'message' => 'Link expired. Please login again to generate a new one',
            ], $res);
        }
        catch (TooManyRequestsException $e) {
            return $this->view('auth/mailCheck', [
                'message' => 'Cool down, clicking the key too much can hurt your finger and keys',
            ], $res);
        }
    }

    /**
     * @param \Psr\Http\Message\RequestInterface|\Slim\Http\Request   $req
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $res
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response $args
     *
     * @return \Psr\Http\Message\ResponseInterface|\Slim\Http\Response
     */
    public function register($req, $res, $args)
    {
        $data = $req->getParsedBody();

        try {
            $userId = $this->auth->registerWithUniqueUsername($data['email'], $data['password'], $data['username'], function ($selector, $token) use ($data) {
                if ($this->send($data['email'], urlencode($selector), urlencode($token), $data['username'])) {
                    $this->sent = true;
                } else {
                    $this->sent = false;
                }
            });

            if ($this->sent) {
                return $this->view('auth/register', [
                    'message' => 'Verification mail has been sent to you, if not received then check your spam box'
                ], $res);
            }

            return $this->view('auth/register', [
                'error' => 'Email delivery failed. Please contact the developer with the email you signup now'
            ], $res);
        }
        catch (InvalidEmailException $e) {
            return $this->view('auth/register', [
                'error' => 'The E-Mail you entered is invalid. Enter a proper mail ID'
            ], $res);
        }
        catch (InvalidPasswordException $e) {
            return $this->view('auth/register', [
                'error' => 'The password you entered is invalid. Why not try something good?'
            ], $res);
        }
        catch (UserAlreadyExistsException $e) {
            return $this->view('auth/register', [
                'error' => 'User already exits, login or create a new account instead'
            ], $res);
        }
        catch (TooManyRequestsException $e) {
            return $this->view('auth/register', [
                'error' => 'Cool down, don\'t click it too much. Hands might pain'
            ], $res);
        }
        catch (DuplicateUsernameException $e) {
            return $this->view('auth/register', [
                'error' => 'Username already exists, choose another one'
            ], $res);
        }
        catch (EmailOrUsernameRequiredError $e) {
            return $this->view('auth/register', [
                'error' => 'Oops, email or username is required. Please fill them'
            ], $res);
        }
    }

    /**
     * @param string $to       Email of the recipient
     * @param string $selector Special selector for the user
     * @param string $token    Special token for the user
     * @param string $username Username of the user
     *
     * @return bool
     */
    private function send($to, $selector, $token, $username)
    {
        $this->mail->addAddress($to, $username);
        $this->mail->Subject = 'Smart Stats - Verify email - Team Lightning';
        $this->mail->Body    = '<!DOCTYPE html><html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"><head> <meta charset="utf-8"> <meta name="viewport" content="width=device-width"> <meta http-equiv="X-UA-Compatible" content="IE=edge"> <meta name="x-apple-disable-message-reformatting"> <title>Verify Email - Smart Stats - Team Lightning</title> <!--[if mso]> <style> * { font-family: sans-serif !important; } </style> <![endif]--> <!--[if !mso]><!--> <!-- insert web font reference, eg: <link href=\'https://fonts.googleapis.com/css?family=Roboto:400,700\' rel=\'stylesheet\' type=\'text/css\'> --> <!--<![endif]--> <style> html, body { margin: 0 auto !important; padding: 0 !important; height: 100% !important; width: 100% !important; } * { -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; } div[style*="margin: 16px 0"] { margin:0 !important; } table, td { mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important; } table { border-spacing: 0 !important; border-collapse: collapse !important; table-layout: fixed !important; margin: 0 auto !important; } table table table { table-layout: auto; } img { -ms-interpolation-mode:bicubic; } *[x-apple-data-detectors], .x-gmail-data-detectors, .x-gmail-data-detectors *, .aBn { border-bottom: 0 !important; cursor: default !important; color: inherit !important; text-decoration: none !important; font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important; } .a6S { display: none !important; opacity: 0.01 !important; } img.g-img + div { display:none !important; } .button-link { text-decoration: none !important; } @media only screen and (min-device-width: 375px) and (max-device-width: 413px) { .email-container { min-width: 375px !important; } } </style> <style> .button-td, .button-a { transition: all 100ms ease-in; } .button-td:hover, .button-a:hover { background: #555555 !important; border-color: #555555 !important; } /* Media Queries */ @media screen and (max-width: 480px) { .fluid { width: 100% !important; max-width: 100% !important; height: auto !important; margin-left: auto !important; margin-right: auto !important; } .stack-column, .stack-column-center { display: block !important; width: 100% !important; max-width: 100% !important; direction: ltr !important; } .stack-column-center { text-align: center !important; } .center-on-narrow { text-align: center !important; display: block !important; margin-left: auto !important; margin-right: auto !important; float: none !important; } table.center-on-narrow { display: inline-block !important; } .email-container p {font-size: 17px !important;line-height: 22px !important;} } </style> <!--[if gte mso 9]> <xml> <o:OfficeDocumentSettings> <o:AllowPNG/> <o:PixelsPerInch>96</o:PixelsPerInch> </o:OfficeDocumentSettings> </xml> <![endif]--> </head><body width="100%" bgcolor="#222222" style="margin: 0; mso-line-height-rule: exactly;"> <center style="width: 100%; background: #222222; text-align: left;"> <div style="display:none;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;mso-hide:all;font-family: sans-serif;"> Verify your email - Smart Stats by Team Lightning </div> <div style="max-width: 680px; margin: auto;" class="email-container"> <!--[if mso]> <table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" width="680" align="center"> <tr> <td> <![endif]--> <!-- Email Body : BEGIN --> <table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 680px;" class="email-container"> <!-- Hero Image, Flush : BEGIN --> <tr> <td bgcolor="#ffffff"> <img src="http://teamlightning.xyz/images/LIGHTNING.png" aria-hidden="true" width="680" height="" alt="alt_text" border="0" align="center" class="fluid" style="width: 100%; max-width: 680px; height: auto; background: #dddddd; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;" class="g-img"> </td> </tr> <!-- Hero Image, Flush : END --> <!-- 1 Column Text + Button : BEGIN --> <tr> <td bgcolor="#ffffff"> <table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" width="100%"> <tr> <td style="padding: 40px 40px 20px; text-align: center;"> <h1 style="margin: 0; font-family: sans-serif; font-size: 24px; line-height: 27px; color: #333333; font-weight: normal;">Verify your E-Mail. Team Lightning</h1> </td> </tr> <tr> <td style="padding: 0 40px 40px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555; text-align: center;"> <p style="margin: 0;">Hey '.$username.'. Someone, hopefully you, have created an account with this email. If it was not you, then don\'t worry. Just discard this mail. Or luckily if it was you, then please verify this email by clicking on the button below so you can get access to the website</p> </td> </tr> <tr> <td style="padding: 0 40px 40px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;"> <table role="presentation" aria-hidden="true" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: auto;"> <tr> <td style="border-radius: 3px; background: #222222; text-align: center;" class="button-td"> <a href="'.getenv("SITE_URL").$this->container->router->pathFor("auth.mail.check", ["selector"=>$selector,"token"=>$token]).'" style="background: #222222; border: 15px solid #222222; font-family: sans-serif; font-size: 13px; line-height: 1.1; text-align: center; text-decoration: none; display: block; border-radius: 3px; font-weight: bold;" class="button-a"> <span style="color:#ffffff;" class="button-link">&nbsp;&nbsp;&nbsp;&nbsp;Verify now!&nbsp;&nbsp;&nbsp;&nbsp;</span> </a> </td> </tr> </table> <!-- Button : END --> </td> </tr> </table> </td> </tr> </table> <!-- Email Body : END --> </center></body></html>';

        return $this->mail->send();
    }

    /**
     * @param string                                                    $template
     * @param array                                                     $args
     * @param \Psr\Http\Message\ResponseInterface|\Slim\Http\Response   $res
     *
     * @return \Psr\Http\Message\ResponseInterface|\Slim\Http\Response
     */
    private function view($template, $args = [], $res)
    {
        return $this->container->view->render($res, $template . '.twig', $args);
    }
}

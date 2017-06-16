<?php

namespace App\Authenticator;

use Delight\Auth\AmbiguousUsernameException;
use Delight\Auth\Auth;
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

require __DIR__ . '/../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';

class AuthHandler
{
    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var PHPMailer
     */
    protected $mail;

    /**
     * @var \Slim\Container
     */
    protected $container;

    public function __construct($container)
    {
        $this->mail = new \PHPMailer;
        $this->auth = new Auth($this->container->db->pdo);
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

    }

    private function view($template, $args = [], $res)
    {
        return $this->container->view->render($res, $template . '.twig', $args);
    }
}
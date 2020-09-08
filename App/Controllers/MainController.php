<?php

namespace App\Controllers;

use App\Models\UserModel;
use Base\ControllerAbstract;
use Base\View;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class MainController extends ControllerAbstract
{
    public function index()
    {
        if (!$this->USER) {
            $this->redirect('/login');
        }

        return new View('main.main');
    }

    public function logOut()
    {
        if ($this->USER) {
            $this->USER->logOut();
        }

        $this->redirect('/login');
    }

    public function login()
    {
        if ($this->USER) {
            $this->redirect('/');
        }
        return new View('main.auth');
    }

    public function loginPost()
    {
        if ($this->USER) {
            $this->redirect('/');
        }

        $email = $this->p('email');
        $password = $this->p('password');


        try {
            $user = UserModel::loginByData([
              'email' => $email,
              'password' => $password,
            ]);

            $success = $user->authorize();

            if (!$success) {
                $error = 'Wrong login or password';
            }

        } catch (\Exception $e) {
            $error = 'Sever error';
            trigger_error($e->getMessage());
            $success = false;
        }

        if ($success) {
            $this->redirect('/');
        } else {
            var_dump('error');

            die();
            //            $this->view->error = $error;
            //            $this->tpl = 'register.phtml';
        }

        $this->_noRender = true;

        return '';
    }

    public function registrationPost()
    {

        if ($this->USER) {
            $this->redirect('/');
        }

        try {
            UserModel::initByData($_POST);
        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
            die();
        }

        $this->_noRender = true;

        var_dump('created');
        die();

    }

    public function test() {
        // Create the Transport
        $transport = (new Swift_SmtpTransport('mail.evd1ser.ru', 25))
          ->setUsername('info@evd1ser.ru')
          ->setPassword('demodemo1!');

        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

        // Create a message
        $message = (new Swift_Message('Test Message from php'))
          ->setFrom(['info@evd1ser.ru' => 'my info'])
          ->setTo(['evd1ser@gmail.com'])
          ->setBody('Hellp loft php')
        ;

        // Send the message
        $result = $mailer->send($message);

        $this->noRender();
        var_dump($result);
    }
}

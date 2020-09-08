<?php

namespace App\Controllers;

use App\Models\UserModel;
use Base\ControllerAbstract;
use Base\View;

class AdminController extends ControllerAbstract
{
    public function index()
    {
        if (!$this->USER) {
            $this->redirect('/');
        }
        if (!$this->USER->isAdmin()) {
            $this->redirect('/');
        }

        $view = new View('admin.index');
        $view->users = UserModel::all();//знаю что нужен лимит - в рамках программы обучения пройдет и так ведь
        $view->user = $this->USER;

        return $view;
    }

    public function createUser()
    {
        $name = $this->p('name');
        $email = $this->p('email');
        $password = $this->p('password');

        if (empty($name) || empty($email) || empty($password)) {
            $this->redirect('/admin');
        }

        $password = md5($password);

        UserModel::create([
          'name' => $name,
          'email' => $email,
          'password' => $password,
        ]);

        $this->redirect('/admin');
    }

    public function updateUser()
    {
        $user_id = $this->p('user_id');
        if(empty($user_id)){
            $this->redirect('/admin');
            return;
        }

        $user = UserModel::findOrFail($user_id);

        $user->name = $this->p('name'); // надо позаботиться о безопасности данных
        $user->email = $this->p('email');

        $password = $this->p('password');

        if($password){
            $user->password = md5($password);
        }

        $user->save();

        $this->redirect('/admin');
    }
}

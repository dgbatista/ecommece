<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\LoginHandler;

class LoginController extends Controller {

    /*Page*/
    public function signin(){
        echo 'login';
    }

    public function sigup(){
        echo 'cadastro';
    }

    /*Admin*/
    public function admin_signin(){
        $flash = '';
        if(!empty($_SESSION['flash'])){
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }
        $this->render('admin/login', [
            'flash' => $flash
        ]);
    }

    public function signin_action(){
        $login = filter_input(INPUT_POST, 'login');
        $password = filter_input(INPUT_POST, 'password');

        if($login && $password){

            $dataUser = LoginHandler::verifyLogin($login, $password);

            if($dataUser['token'] && $dataUser['admin'] === 1){
                $_SESSION['token'] = $dataUser['token'];
                $this->redirect('/admin');
            } else {
                $_SESSION['flash'] = 'Login e/ou senha não conferem.';
                $this->redirect('/admin/login');
            }
        } else {
             $this->redirect('/admin/login');
        }
    }

    public function logout(){
        if(!empty($_SESSION['token'])){
            $_SESSION['token'] = '';
            $this->redirect('/admin/login');
        }
    }
}
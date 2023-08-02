<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;

class UserController extends Controller {

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

            $dataUser = UserHandler::verifyLogin($login, $password);

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

    public function edit($args){
        $id = $args['id'];

        echo $id;
        exit;
    }

    public function delete($args){
        $id = $args['id'];

        echo $id;
        exit;
    }
    
    public function create(){
        $flash = '';
        if(!empty($_SESSION['flash'])){
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }
        $this->render('admin/create', [
            'flash' => $flash
        ]);
        
        $this->render('admin/header');
        $this->render('admin/users-create');
        $this->render('admin/footer');

    }    

    public function createAction(){
        $desperson = filter_input(INPUT_POST, 'desperson');
        $deslogin = filter_input(INPUT_POST, 'deslogin');
        $nrphone = filter_input(INPUT_POST, 'nrphone');
        $desemail = filter_input(INPUT_POST, 'desemail');
        $despassword = filter_input(INPUT_POST, 'despassword');
        $inadmin = filter_input(INPUT_POST, 'inadmin');

        $flash = '';

        if($desperson && $deslogin && $nrphone && $desemail && $despassword){
            /*E-MAIL*/
            $email = UserHandler::validateEmail($desemail);
            if($email != false){
                $_SESSION['flash'] = 'E-mail já cadastrado.';
                $this->redirect('/admin/users/create');
            }
            /**LOGIN */
            $email = UserHandler::validateLogin($desemail);
            if($email != false){
                $_SESSION['flash'] = 'Login não disponível.';
                $this->redirect('/admin/users/create');
            }
            
            

            

        } else {
            $this->redirect('/admin/users/create');
        }

        $flash = $_SESSION['flash'];

        $this->render('admin/header');
        $this->render('admin/users-create', ["flash" => $flash]);
        $this->render('admin/footer');

    }
    
}
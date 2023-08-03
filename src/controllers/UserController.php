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

    public function users(){
        $loggedUser = UserHandler::checkLogin();
        $users = UserHandler::getAllUsers();

        if($loggedUser->token && $loggedUser->inadmin === 1){

            $this->render('admin/header');
            $this->render('admin/users', [
                'users'=> $users
            ]);
            $this->render('admin/footer');

        } else {
            $this->redirect('/admin/login');
        }
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
        $this->render('admin/create');
        
        $this->render('admin/header');
        $this->render('admin/users-create', [
            'flash' => $flash
        ]);
        $this->render('admin/footer');

    }    

    public function createAction(){
        $desperson = filter_input(INPUT_POST, 'desperson');
        $deslogin = filter_input(INPUT_POST, 'deslogin');
        $nrphone = filter_input(INPUT_POST, 'nrphone');
        $desemail = filter_input(INPUT_POST, 'desemail');
        $despassword = filter_input(INPUT_POST, 'despassword');
        $inadmin = filter_input(INPUT_POST, 'inadmin', FILTER_VALIDATE_INT);
        
        if($desperson && $deslogin && $nrphone && $desemail && $despassword){
            /*E-MAIL*/
            $email = UserHandler::validateEmail($desemail);
            if($email != false){
                $_SESSION['flash'] = 'E-mail já cadastrado.';
                $this->redirect('/admin/users/create');
            }
            /**LOGIN */
            $login = UserHandler::validateLogin($deslogin);
            if($login != false){
                $_SESSION['flash'] = 'Login não disponível.';
                $this->redirect('/admin/users/create');
            }

            /*VALIDAR NUMERO DE TELEFONE*/

            $despassword = password_hash($despassword, PASSWORD_DEFAULT);
            
            $newPerson = UserHandler::savePerson($desperson, $desemail, $nrphone);

            if($newPerson){

                $newUser = UserHandler::saveUser($newPerson['idperson'], $deslogin, $despassword, $inadmin);

                $user = array_merge($newPerson, $newUser);
            }

            
            echo '<pre>';
            print_r($user);
            exit;

            

        } else {
            $_SESSION['flash'] = 'Preencha todos os campos';
            $this->redirect('/admin/users/create');
        }
    }
    
}
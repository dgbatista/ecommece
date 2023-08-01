<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;

class AdminController extends Controller {

    private $loggedUser;

    public function __construct() {
        $this->loggedUser = UserHandler::checkLogin();
        if(UserHandler::checkLogin() === false){
            $this->redirect('/admin/login');
        }        
    }

    public function index() {

        $this->render('admin/header');        
        $this->render('admin/index');
        $this->render('admin/footer');
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

    public function logout(){
        if(!empty($_SESSION['token'])){
            $_SESSION['token'] = '';
            $this->redirect('/admin/login');
        }
    }
}
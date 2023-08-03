<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;

class HomeController extends Controller {

    private $loggedUser;

    public function __construct() {
        $this->loggedUser = UserHandler::checkLogin();
        if(UserHandler::checkLogin() === false){
            $this->redirect('/login');
        }        
    }

    public function index() {
        $this->render('site/index');
    }

    public function logout(){
        if(!empty($_SESSION['token'])){
            $_SESSION['token'] = '';
            $this->redirect('/admin/login');
        }
    }
}
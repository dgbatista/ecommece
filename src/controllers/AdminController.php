<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\LoginHandler;

class AdminController extends Controller {

    private $loggedUser;

    public function __construct() {
        $this->loggedUser = LoginHandler::checkLogin();
        if(LoginHandler::checkLogin() === false){
            $this->redirect('/admin/login');
        }        
    }

    public function index() {
        $this->render('admin/header');
        
        $this->render('admin/index');

        $this->render('admin/footer');
    }
}
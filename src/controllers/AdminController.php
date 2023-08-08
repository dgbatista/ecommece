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

        $this->render('admin/index');
    }    
}
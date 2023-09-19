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
        
        if($this->loggedUser->inadmin === 0){
            $this->redirect('/');
        } 
           
    }

    public function index() {

        if($this->loggedUser->inadmin === 0){
            $this->redirect('/');
        } else {
            $this->render('admin/index');
        }

    }    
}
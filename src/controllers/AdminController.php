<?php
namespace src\controllers;

use \core\Controller;

class AdminController extends Controller {

    public function index() {
        $this->render('admin/header');
        
        $this->render('admin/index');

        $this->render('admin/footer');
    }
}
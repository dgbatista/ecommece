<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\CategoryHandler;

class CategoryController extends Controller {

    private $loggedUser;

    public function __construct() {
        $this->loggedUser = UserHandler::checkLogin();
        if(UserHandler::checkLogin() === false){
            $this->redirect('/login');
        }        
    }

    public function index() {

        $categories = CategoryHandler::getCategories();

       $this->render('admin/header');
       $this->render('admin/categories', [
        'categories' => $categories
       ]);
       $this->render('admin/footer');
    }

    public function create() {

       $descategory = filter_input(INPUT_POST, 'descategory');

       if(!empty($descategory)){
            CategoryHandler::save($descategory);
            $this->redirect('/admin/categories');
       }
       
       $this->render('admin/header');
       $this->render('admin/categories-create');
       $this->render('admin/footer');
       
    }

    public function update($args) {
        $idcategory = $args['id'];        

        if(!empty($idcategory)){
            $category = CategoryHandler::getCategoryById($idcategory);

            if(!empty($category)){
                $descategory = filter_input(INPUT_POST, 'descategory');
        
                if(!empty($descategory)){
                    CategoryHandler::update($idcategory , $descategory);
                    $this->redirect('/admin/categories');
                }

            } else {
                $this->redirect('/admin/categories');
            }

            $this->render('admin/header');
            $this->render('admin/categories-update' , [
                'category' => $category
            ]);
            $this->render('admin/footer');
        }
    }

    public function delete($args){
        $id = $args['id'];
        
        if(!empty($id)){
            $category = CategoryHandler::getCategoryById($id);

            if($category){
                CategoryHandler::delete($id);
            }
        }

        $this->redirect('/admin/categories');

    }

}
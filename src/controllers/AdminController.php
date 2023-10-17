<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\OrderHandler;

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

    }    

    public function orders(){

        $orders = OrderHandler::listAll();

        if($orders){
            foreach($orders as $order){
                $orderObj[] = (Object)$order;
            }
        }        

        $this->render('admin/orders', [
            'orders' => $orderObj
        ]);

    }

    public function delete($args){

        $idorder = (int) $args['idorder'];

        OrderHandler::deleteById($idorder);

        $this->redirect('/admin/orders');

    }

    public function status($args){
        $error = '';
        $success = '';

        $idorder = (int) $args['idorder'];

        $order = OrderHandler::getJoinsOrderById($idorder);

        $status = OrderHandler::getAllOrderStatus();

        $idstatus = filter_input(INPUT_POST, 'idstatus');

        if(isset($idstatus) && !empty($idstatus)){            
            if($order){
                $order['idstatus'] = $idstatus;
                OrderHandler::update($order);

                $success = "Atualizado com sucesso!";

                $this->redirect('/admin/orders');
            }
        }

        if($order){
            $order = (Object) $order;
            $this->render('admin/order-status', [
                'order' => $order,
                'list_status' => $status,
                'success' => (isset($sucess)) ? $success :  '',
                'error' => (isset($error)) ? $error :  ''
            ]);
        } else {
            $this->redirect('/admin/orders');
        }      

    }

    public function order($args){

        $idorder = (int) $args['idorder'];

        $order = (Object) OrderHandler::getJoinsOrderByIdCart($idorder);

        echo '<pre>';
        print_r($order);
        exit;

        if($order){
            $this->render('admin/order', [
                'order' => $order,
                'products' => []
            ]);
        } else {
            $this->redirect('/admin/orders');
        }
       
        

    }
}
<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\OrderHandler;
use \src\handlers\CartHandler;

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

        $this->render('admin/index');

    }    
    public function users(){
        
        $search = filter_input(INPUT_GET, 'search');
        $page = filter_input(INPUT_GET, 'page');
        
        $search = (isset($search)) ? $search : "";
        $page = (int) (isset($page))? $page : 0;
        
        $pagination = UserHandler::getUserPerPage($page);

        $pages = [];
        for($x=0; $x<$pagination['pageCount']; $x++){
            array_push($pages, [
                'href'=> 'users?'.http_build_query([
                    'page'=>$x,
                    'search'=>$search
                ]),
                'text'=>$x+1
            ]);
        }

        $this->render('admin/users', [
            'users'=> $pagination['users'],
            'pageActive' => 'users',
            'search' => $search,
            'pages' => $pages
        ]);
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

        $order = (Object) OrderHandler::getJoinsOrderById($idorder);

        $cart = CartHandler::getFullCart($order->idcart);

        // echo '<pre>';
        // print_r($cart);
        // exit;

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
<?php
namespace src\handlers;

use \src\models\Order;
use \src\models\OrdersStatu;


class OrderHandler {

    public static function saveOrder($cart, $address){

        if(isset($cart[1]['freight']['total']) && !empty($cart[1]['freight']['total'])){
            try{

                Order::insert([
                    'idcart' => $cart[0]->idcart,
                    'iduser' => $cart[0]->iduser,
                    'idstatus' => OrdersStatu::EM_ABERTO,
                    'idaddress' => $address->idaddress,
                    'vltotal' => $cart[1]['freight']['total'],
                ])->execute();

                $data = Order::select()
                    ->where('idcart', $cart[0]->idcart)
                    ->where('iduser' , $cart[0]->iduser)
                    ->where('vltotal',$cart[1]['freight']['total'])
                ->one();

                if($data){
                    $order = self::getOrderById($data['idorder']);

                    return $order;
                }

            }catch(Exception $e){
    
                return $e.getMessage();
            } 
        } else {
            return false;
        }     
    }

    public static function getOrderById($idOrder){

        $data = Order::select()
            ->where('idorder', $idOrder)
        ->one();
        
        if($data){
            $order = new Order();
            $order->idorder = $data['idorder'];
            $order->idcart = $data['idcart'];
            $order->iduser = $data['iduser'];
            $order->idstatus = $data['idstatus'];
            $order->idaddress = $data['idaddress'];
            $order->vltotal = $data['vltotal'];
            $order->dtregister = $data['dtregister'];

            return $order;
        }

        return false;
    }

    public static function getJoinsOrderById($idOrder){

        $data = Order::select()
            ->join('ordersstatus','orders.idstatus', '=', 'ordersstatus.idstatus')
            ->join('carts', 'orders.idcart', '=', 'carts.idcart')
            ->join('users', 'orders.iduser', '=', 'users.iduser')
            ->join('addresses', 'orders.idaddress', '=', 'addresses.idaddress')
            ->join('persons', 'users.idperson', '=', 'persons.idperson')
            ->where('idorder', $idOrder)
        ->get();

        if($data){
            return $data;
        }

        return false;
    }

    public static function getJoinsOrderByIdCart($idOrder){

        $data = Order::select()
            ->join('ordersstatus','orders.idstatus', '=', 'ordersstatus.idstatus')
            ->join('carts', 'orders.idcart', '=', 'carts.idcart')
            ->join('users', 'orders.iduser', '=', 'users.iduser')
            ->join('addresses', 'orders.idaddress', '=', 'addresses.idaddress')
            ->join('persons', 'users.idperson', '=', 'persons.idperson')
            ->join('cartsproducts', 'carts.idcart', '=', 'cartsproducts.idcart')
            ->join('products', 'cartsproducts.idproduct', '=', 'products.idproduct')
            ->where('idorder', $idOrder)
        ->get();

        if($data){
            return $data;
        }

        return false;
    }

}
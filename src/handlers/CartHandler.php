<?php
namespace src\handlers;

use \src\models\Cart;

class CartHandler {

    const SESSION = "Cart";

    private static function transformToObject($array){
        $cart = new Cart();
        $cart->idcart = $array['idcart'];
        $cart->dessessionid = $array['dessessionid'];
        $cart->iduserid = $array['iduserid'];
        $cart->deszipcode = $array['deszipcode'];
        $cart->vlfreight = $array['vlfreight'];
        $cart->nrdays = $array['nrdays'];

        return $cart;
    }

    public static function getFromSession(){
        $cart = new Cart();

        if(isset($_SESSION[Cart::SESSION]) && (int)$_SESSION['idcart']>0){

            $cart = self::get((int)$_SESSION[Cart::SESSION]['idcart']);

        } else {
            $cart = self::getFromSessionID(session_id());

        }

    }

    public static function get($idcart){
        $data = Cart::select()->where('idcart', $idcart)->one();

        if($data){
            $cart = self::transformToObject($data);
            return $cart;
        }       
    }

    public static function getFromSessionID($sessionID){
        $data = Cart::select()->where('dessessionid', $sessionID)->one();

        if($data){
            $cart = self::transformToObject($data);
            return $cart;
        }   
    }

    public static function save(Cart $cart){
        Cart::insert([
            'idcart' => $cart->idcart,
            'dessessionid'=>$cart->dessessionid,
            'iduser'=>$cart->iduser,
            'deszipcode'=>$cart->deszipcode,
            'vlfreight'=>$cart->vlfreight,
            'nrdays'=>$cart->nrdays
        ])->execute();
    }

}
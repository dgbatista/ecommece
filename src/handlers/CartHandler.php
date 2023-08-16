<?php
namespace src\handlers;

use \src\models\Cart;
use \src\models\User;

class CartHandler {

    const SESSION = "Cart";

    private static function transformCartToObject($array){
        $cart = new Cart();
        $cart->idcart = $array['idcart'] ?? 0;
        $cart->dessessionid = $array['dessessionid'];
        $cart->iduser = $array['iduser'] ?? null;
        $cart->deszipcode = $array['deszipcode'] ?? null;
        $cart->vlfreight = $array['vlfreight'] ?? null;
        $cart->nrdays = $array['nrdays'] ?? null;

        return $cart;
    }

    public static function getFromSession(){
        $cart = new Cart();

        if(isset($_SESSION['Cart']) && (int)$_SESSION['idcart']>0){

            $cart = self::get((int)$_SESSION['Cart']['idcart']);

        } else {

            $cart = self::getFromSessionID();

            if(!$cart){

                $data = [
                    'dessessionid'=>session_id()

                ];

                $user = UserHandler::checkLogin();

                if($user){

                    $data['iduser'] = $user->iduser;                    

                }

                $cart = self::transformCartToObject($data);

                self::save($cart);

                self::setToSession($cart);
            }
        }
    }

    public static function setToSession(Cart $cart){
    
        $_SESSION['Cart'] = $cart;

    }

    public static function get($idcart){
        $data = Cart::select()->where('idcart', $idcart)->one();

        if($data){
            $cart = self::transformToObject($data);
            return $cart;
        }       
        return false;
    }

    public static function getFromSessionID(){
        $data = Cart::select()->where('dessessionid', session_id())->one();

        if($data){
            $cart = self::transformCartToObject($data);
            return $cart;
        }
        
        return false;
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
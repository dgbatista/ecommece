<?php
namespace src\handlers;

use \src\models\Cart;
use \src\models\User;
use \src\models\CartsProduct;
use \src\models\Product;

class CartHandler {

    const SESSION = "Cart";

    private static function transformCartToObject($array){

        $cart = new Cart();

        if(!empty($array['idcart'])){
            $cart->idcart = $array['idcart'];       
        }
        $cart->dessessionid = $array['dessessionid'] ?? '';
        $cart->iduser = $array['iduser'] ?? null;
        $cart->deszipcode = $array['deszipcode'] ?? null;
        $cart->vlfreight = $array['vlfreight'] ?? null;
        $cart->nrdays = $array['nrdays'] ?? null;

        return $cart;
    }

    public static function getFromSession(){

        $cart = new Cart();

            $cart = self::getFromSessionID(session_id());

            if($cart){
                self::setToSession($cart);
            }

            if(!$cart){

                $data['dessessionid'] = session_id();

                $user = UserHandler::checkLogin();
                
                if($user){
                    $data['iduser'] = $user->iduser;                   
                }

                $cart = self::transformCartToObject($data);  
                
                self::save($cart);
                
                $c = self::getFromSessionID($data['dessessionid']);

                self::setToSession($cart);
            }

        return $cart;
    }

    public static function setToSession(Cart $cart){
    
        $_SESSION['cart'] = $cart;

    }

    public static function get($idcart){
        $data = Cart::select()->where('idcart', $idcart)->one();

        if($data){
            $cart = self::transformToObject($data);
            return $cart;
        }       
        return false;
    }

    public static function getFromSessionID($idsession = ''){
        $data = Cart::select()->where('dessessionid', $idsession)->one();

        if($data){            
            $cart = self::transformCartToObject($data);
            return $cart;
        }
        
        return false;
    }

    public static function save(Cart $cart){
        Cart::insert([
            'dessessionid'=>$cart->dessessionid,
            'iduser'=>$cart->iduser,
            'deszipcode'=>$cart->deszipcode,
            'vlfreight'=>$cart->vlfreight,
            'nrdays'=>$cart->nrdays
        ])->execute();
    }

    public static function addProducToCart(Product $product, $idcart){
        
        CartsProduct::insert([
            'idcart' => $idcart,
            'idproduct' => $product->idproduct
        ])->execute();

    }

    public static function removeProductToCart(Product $product, $idcart ,$all = false){
        
        if($all){
                CartsProduct::update([
                    'dtremoved'=> date('Y-m-d H:i:s')
                ])
                    ->where('idcart', $idcart)
                    ->where('idproduct', $product->idproduct)
                    ->whereNull('dtremoved')
                ->execute();
        } else {
                CartsProduct::update([
                    'dtremoved'=> date('Y-m-d H:i:s')
                ])
                    ->where('idcart', $idcart)
                    ->where('idproduct', $product->idproduct)
                    ->whereNull('dtremoved')
                    ->limit(1)
                ->execute();
        }
    }

    public static function getProducts($idcart){
        $data = CartsProduct::select()
            ->join('products', 'cartsproducts.idproduct', '=', 'products.idproduct')
            ->where('idcart', $idcart)
            ->whereNull('dtremoved')
            ->groupBy('products.idproduct')
            ->orderBy('products.desproduct')
        ->get();

        $total = CartsProduct::select()
            ->join('products', 'cartsproducts.idproduct', '=', 'products.idproduct')
            ->where('idcart', $idcart)
        ->count();

        $cart = self::arrayToCartObject($data);

        return $cart;
    }

    public static function arrayToCartObject($arrays){

        $carts = [];
        $totalPrice = 0;
        $nrqtd = 0;

        if(count($arrays)>0){
            foreach($arrays as $array){
                $cart = new CartsProduct();
                $cart->idcartproduct = $array['idcartproduct'] ?? 0;
                $cart->idcart = $array['idcart'] ?? 0;
                $cart->idproduct = $array['idproduct'] ?? 0;
                $cart->dtremoved = $array['dtremoved'] ?? NULL;
                $cart->desproduct = $array['desproduct'] ?? '';
                $cart->vlprice = $array['vlprice'] ?? 0;
                $cart->vlwidth = $array['vlwidth'] ?? 0;
                $cart->vlheight = $array['vlheight'] ?? 0;
                $cart->vllength = $array['vllength'] ?? 0;
                $cart->vlweight = $array['vlweight'] ?? 0;
                $cart->desurl = $array['desurl']?? '';
                
                $totalPrice += $cart->vlprice;
                $nrqtd++;

                $carts[] = $cart;
            }
            $carts[0]->vltotal = $totalPrice;
            $carts[0]->nrqtd = $nrqtd;
        }       

        return $carts;
    }

}
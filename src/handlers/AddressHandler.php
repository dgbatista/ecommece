<?php
namespace src\handlers;

use \src\models\Address;

class AddressHandler {

    public static function getAddressById($iduser){

        $address = Address::select()->where('iduser', $iduser);

        print_r($address);
        exit;

        if(count($address) > 0){
            
        }

    }

}
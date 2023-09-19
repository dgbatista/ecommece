<?php
namespace src\handlers;

use \src\models\Addresse;

class AddressHandler {

    public static function getAddressById($idperson){

        $data = Addresse::select()->where('idperson', $idperson)->one();
        $address = new Addresse();
        
        $address->idperson = $data['idperson'] ?? 0;
        $address->desaddress = $data['desaddress'] ?? '';
        $address->descomplement = $data['descomplement'] ?? '';
        $address->desdistrict = $data['desdistrict'] ?? '';
        $address->descity = $data['descity'] ?? '';
        $address->desstate = $data['desstate'] ?? '';
        $address->descountry = $data['descountry'] ?? '';
        $address->nrzipcode = $data['nrzipcode'] ?? '';
    
        return $address;
    }

}
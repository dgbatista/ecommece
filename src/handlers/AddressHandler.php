<?php
namespace src\handlers;

use \src\models\Addresse;

class AddressHandler {

    public static function getAddressById($idperson){

        $data = Addresse::select()->where('idperson', $idperson)->one();

        if($data){
            $address = new Addresse();
        
            $address->idperson = $data['idperson'] ?? 0;
            $address->desaddress = $data['desaddress'] ?? '';
            $address->desnumber = $data['desnumber'] ?? NULL;
            $address->descomplement = $data['descomplement'] ?? '';
            $address->desdistrict = $data['desdistrict'] ?? '';
            $address->descity = $data['descity'] ?? '';
            $address->desstate = $data['desstate'] ?? '';
            $address->descountry = $data['descountry'] ?? '';
            $address->nrzipcode = $data['nrzipcode'] ?? '';
            $address->idaddress = $data['idaddress'] ??'';
        
            return $address;
        }

        return false;       
    }

    public static function getCep($nrcep){
        $nrcep = str_replace('-', '', $nrcep);

        //viacep.com.br/ws/01001000/json/

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://viacep.com.br/ws/$nrcep/json/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $data = json_decode(curl_exec($ch), true);

        curl_close($ch);

        return $data;
    }
    
    public static function loadFromCep($zipcode){

        $data = self::getCep($zipcode);
        $address = new Addresse();
        
        if(isset($data['logradouro']) && !empty($data['logradouro'])){

            $address->desaddress = $data['logradouro'] ?: '';
            $address->desnumber = '';
            $address->descomplement = $data['complemento']  ?: '';
            $address->desdistrict = $data['bairro']  ?: '';
            $address->descity = $data['localidade']  ?: '';
            $address->desstate = $data['uf']  ?: '';
            $address->descountry = 'Brasil'  ?: '';
            $address->nrzipcode = $zipcode  ?: '';

            $address->nrzipcode = self::formatCepToView($address->nrzipcode);

            return $address;
        }
        return false;
    }

    public static function saveAddress($idperson, $address ){

        $address->nrzipcode = self::formatCepBD($address->nrzipcode);

        Addresse::insert([
            'idperson' => $idperson ?? 0,
            'desaddress' => $address->desaddress ?: '',
            'desnumber' => $address->desnumber ?: 0,
            'descomplement'=> $address->descomplement ?: NULL,
            'desdistrict' =>$address->desdistrict ?: '',
            'descity' => $address->descity ?: '',
            'desstate' => $address->desstate ?: '',
            'descountry' => $address->descountry ?: '',
            'nrzipcode' => $address->nrzipcode ?: 0
            ])
        ->execute();

    }

    public static function updateAddress($idperson, $address ){

        $address->nrzipcode = self::formatCepBD($address->nrzipcode);

        Addresse::update([
            'idperson' => $idperson ?? 0,
            'desaddress' => $address->desaddress ?: '',
            'desnumber' => $address->desnumber ?: 0,
            'descomplement'=> $address->descomplement ?: NULL,
            'desdistrict' =>$address->desdistrict ?: '',
            'descity' => $address->descity ?: '',
            'desstate' => $address->desstate ?: '',
            'descountry' => $address->descountry ?: '',
            'nrzipcode' => $address->nrzipcode ?: 0
            ])
            ->where('idperson', $idperson)
        ->execute();

    }

    public static function formatCepBD($cep){

        $cep = str_replace('-', '', $cep);

        return $cep;
    }

    public static function formatCepToView($cep){

        if(strlen($cep) < 9 && $cep != '' && $cep != 0){
            $cep = substr_replace($cep, '0', 0, 0);
            $cep = substr_replace($cep, '-', 5, 0);
        } 

        return $cep;
    }

    public static function loadAddress($idperson){

        $data = AddressHandler::getAddressById($idperson);

        $address = new Addresse();
        $address->desaddress = $data->desaddress ?: '';
        $address->desnumber = $data->desnumber ?: '';
        $address->descomplement = $data->descomplement ?: '';
        $address->desdistrict = $data->desdistrict ?: '';
        $address->descity = $data->descity ?: '';
        $address->desstate = $data->desstate ?: '';
        $address->descountry =  $data->descountry ?: '';
        $address->nrzipcode = $data->nrzipcode ?: '';

        return $address;
    }
}

    
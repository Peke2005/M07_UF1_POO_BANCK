<?php namespace ComBank\Person;

use ComBank\API\ApiTrait;

class person
{
    private $name;
    private $idCard;
    private $email;
    private $codigo;

    use ApiTrait;

    public function __construct(String $Name, String $IDCard, String $Email, String $codigoPostal = null){
        $this->name = $Name;
        $this->idCard = $IDCard;
        if($codigoPostal != null && !(empty($this->codigoPostal($codigoPostal)))){
            $this->codigo = $this->codigoPostal($codigoPostal);
        }else{
            throw new \Exception("No has introducido ningun codigo postal");
        }
        if($this->validateEmail($Email) == false){
            throw new \Exception("Invalid email address:".$Email);
        }
        $this->email = $Email;
    }


    public function getcodigoPostal(){
        return $this->codigo;
    }
}
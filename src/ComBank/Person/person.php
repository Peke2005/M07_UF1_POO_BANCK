<?php namespace ComBank\Person;

use ComBank\API\ApiTrait;

class person
{
    private $name;
    private $idCard;
    private $email;

    use ApiTrait;

    public function __construct(String $Name, String $IDCard, String $Email){
        $this->name = $Name;
        $this->idCard = $IDCard;
        
        if($this->validateEmail($Email) == false){
            throw new \Exception("Invalid email address:".$Email);
        }
        $this->email = $Email;
    }
}
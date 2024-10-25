<?php namespace ComBank\Bank;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:25 PM
 */

use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\OverdraftStrategy\NoOverdraft;
use ComBank\Bank\Contracts\BackAccountInterface;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
use ComBank\Support\Traits\AmountValidationTrait;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class BankAccount implements BackAccountInterface
{
    private $balance;
    private $status;
    private $overdraft;

    use AmountValidationTrait;

    public function __construct(float $newBalance = 0.0){
        $this->validateAmount($newBalance);
        $this->balance = $newBalance;
        $this->status = BackAccountInterface::STATUS_OPEN;
        $this->overdraft = new NoOverdraft();
    }

    public function openAccount(){
        if($this->status == BackAccountInterface::STATUS_CLOSED){
            return false;
        }else{
            return true;
        }
    }

    public function getStatus(){
        return $this->status;
    }

    public function reopenAccount(){
        if($this->status == BackAccountInterface::STATUS_CLOSED){
            return $this->status = BackAccountInterface::STATUS_OPEN;
        }else if($this->status == BackAccountInterface::STATUS_OPEN){
            throw new BankAccountException("La cuenta ya esta Iniciada");
        }
    }

    public function closeAccount(){
        if($this->status == BackAccountInterface::STATUS_OPEN){
            return $this->status = BackAccountInterface::STATUS_CLOSED;
        }else{
            throw new BankAccountException("Error: La cuenta ya esta cerrada no puedes volver a cerrarla de nuevo");
        }
        
    }

    public function getBalance(){
        return $this->balance;
    }

    public function getOverdraft(){
        return $this->overdraft;
    }

    public function transaction(BankTransactionInterface $BancoTransaccion) {
        if($this->status == BackAccountInterface::STATUS_OPEN){
        $this -> balance = $BancoTransaccion -> applyTransaction($this);
        }else{
            throw new BankAccountException("La cuenta no esta iniciada");
        }
    }

    public function applyOverdraft(OverdraftInterface $Overdraft) {
        $this -> overdraft = $Overdraft;
    }
}
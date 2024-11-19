<?php namespace ComBank\Transactions;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 11:30 AM
 */

use ComBank\Bank\Contracts\BackAccountInterface;
use ComBank\Transactions\Contracts\BankTransactionInterface;
use ComBank\Exceptions\ZeroAmountException;
class DepositTransaction  extends BaseTransaction implements BankTransactionInterface
{
    public function __construct(float $newAmount = 0.0){
        parent::validateAmount($newAmount);
        $this -> amount = $newAmount;
    }

    public function getTransactionInfo(): string{
        return 'DEPOSIT_TRANSACTION';
    }

    public function getAmount(){
        return $this -> amount;
    }
    
    public function applyTransaction(BackAccountInterface $account): float{
        if($this -> detectFraud($this) === false){
            return $account -> getBalance() + $this -> getAmount();
            
        }else{  
            throw new \Exception('Es un fraude, no podemos hacer este deposito');
        }
    }
}

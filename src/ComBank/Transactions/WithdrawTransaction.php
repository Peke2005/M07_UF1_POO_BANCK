<?php namespace ComBank\Transactions;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 1:22 PM
 */

use ComBank\Bank\Contracts\BackAccountInterface;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\Transactions\Contracts\BankTransactionInterface;

use function PHPUnit\Framework\throwException;

class WithdrawTransaction extends BaseTransaction implements BankTransactionInterface
{
    public function __construct(float $newAmount = 0.0){
        parent::validateAmount($newAmount);
        $this -> amount = $newAmount;
    }

    public function getTransactionInfo(): string{
        return 'WITHDRAW_TRANSACTION';
    }

    public function getAmount(){
        return $this -> amount;
    }
    
    public function applyTransaction(BackAccountInterface $account): float{
        if (!$account->getOverdraft()->isGrantOverdraftFunds( $account -> getBalance() - $this -> getAmount())) {
            if($account->getOverdraft()->getOverdraftFundsAmount() == 0){
                throw new InvalidOverdraftFundsException( 'No puede retirar el dinero ya que no tiene suficiente dinero en la cuenta');
            }
            throw new FailedTransactionException('Accede el limite de lo que tienes de comodin y no tienes suficiente dinero en la cuenta');
        }

        return $account -> getBalance() - $this -> getAmount();
    }
}

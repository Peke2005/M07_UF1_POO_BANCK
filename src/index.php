<?php

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:24 PM
 */

use ComBank\Bank\BankAccount;
use ComBank\Bank\InternationalBankAccount;
use ComBank\Bank\NationalBankAccount;
use ComBank\OverdraftStrategy\SilverOverdraft;
use ComBank\Transactions\DepositTransaction;
use ComBank\Transactions\WithdrawTransaction;
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\Person\person;

require_once 'bootstrap.php';


//---[Bank account 1]---/
// create a new account1 with balance 400
pl('--------- [Start testing bank account #1, No overdraft] --------');
try {

    $bankAccount1 = new BankAccount(400);

    // Show account

    pl("El estado de la cuenta es: ". $bankAccount1->getStatus());

    // show balance account

    pl("El saldo de la cuenta es: ". $bankAccount1->getBalance());

    // close account

    pl("El estado de la cuenta es: ". $bankAccount1->closeAccount());

    // reopen account

    pl("El estado de la cuenta es: ". $bankAccount1->reopenAccount());

    // deposit +150 
    pl('Doing transaction deposit (+150) with current balance ' . $bankAccount1->getBalance());

    $bankAccount1->transaction(new DepositTransaction(150));

    pl('My new balance after deposit (+150) : ' . $bankAccount1->getBalance());

    // withdrawal -25
    pl('Doing transaction withdrawal (-25) with current balance ' . $bankAccount1->getBalance());

    $bankAccount1->transaction(new WithdrawTransaction(25));

    pl('My new balance after withdrawal (-25) : ' . $bankAccount1->getBalance());

    // withdrawal -600
    pl('Doing transaction withdrawal (-600) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new WithdrawTransaction(600));

} catch (ZeroAmountException $e) {
    pe($e->getMessage());
} catch (BankAccountException $e) {
    pe($e->getMessage());
} catch (FailedTransactionException $e) {
    pe('Error transaction: ' . $e->getMessage());
}catch (InvalidOverdraftFundsException $e) {
    pe($e->getMessage());
}
pl('My balance after failed last transaction : ' . $bankAccount1->getBalance());

$bankAccount1->closeAccount();
pl("La cuenta ya se ha cerrado");


//---[Bank account 2]---/
pl('--------- [Start testing bank account #2, Silver overdraft (100.0 funds)] --------');
try {
    
    $bankAccount2 = new BankAccount(200);
    $bankAccount2 -> applyOverdraft(new SilverOverdraft());
    // show balance account
    pl("El saldo de la cuenta es: ". $bankAccount2->getBalance());

    // deposit +100
    pl('Doing transaction deposit (+100) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new DepositTransaction(100));
    pl('My new balance after deposit (+100) : ' . $bankAccount2->getBalance());
    // withdrawal -300

    pl('Doing transaction withdrawal (-300) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(300));
    pl('My new balance after withdrawal (-300) : ' . $bankAccount2->getBalance());

    // withdrawal -50
    pl('Doing transaction withdrawal (-50) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(50));
    pl('My new balance after withdrawal (-50) with funds : ' . $bankAccount2->getBalance());

    // withdrawal -120
    pl('Doing transaction withdrawal (-120) with current balance ' . $bankAccount2->getBalance());
    
    $bankAccount2->transaction(new WithdrawTransaction(120));
} catch (FailedTransactionException $e) {
    pe('Error transaction: ' . $e->getMessage());
}catch (InvalidOverdraftFundsException $e) {
    pe($e->getMessage());
}
pl('My balance after failed last transaction : ' . $bankAccount2->getBalance());

try {
    pe('Doing transaction withdrawal (-20) with current balance : ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(20));
}catch (InvalidOverdraftFundsException $e) {
    pe($e->getMessage());
}
pl('My new balance after withdrawal (-20) with funds : ' . $bankAccount2->getBalance());


$bankAccount2->closeAccount();
pl("La cuenta ya se ha cerrado");

try {
    $bankAccount2->closeAccount();
}catch(BankAccountException $e){
    pe($e->getMessage());
}

//----[Bank account 3 National (No conversion)]---------/
pl('--------- [Start testing bank account #3, (No conversion)] --------');
try {
$bankAccount3 = new NationalBankAccount(500, "€ (EUR)");
pl("My Balance: ". $bankAccount3->getBalance(). " " . $bankAccount3->getCurrency());
$persona = new person("Pol", "12345678B", "polcarbajalgarcia@gmail.com");

pl("Validating Email: polcarbajalgarcia@gmail.com");

pl("Email is valid");

}catch (Exception $e) {
    pe('Error: '. $e->getMessage());
}

//----[Bank account 4 Internacional]---/
pl('--------- [Start testing bank account #4, INTERNACIONAL] --------');
try{
$bankAccount4 = new InternationalBankAccount(300, "€ (EUR)");

pl("My Balance: ". $bankAccount4->getBalance() . " " . $bankAccount4->getCurrency());

pl("Converting balance to dollars (Rates:  " . $bankAccount4->getConvertedCurrency(). " = " . $bankAccount4->getConvertedAmount() . ")");

pl("El balance convertido es: ". $bankAccount4->getConvertedBalance(). " ". $bankAccount4->getConvertedCurrency());


pl("Validating Email: polcarbajalgarcia@invalid-com");

$persona2 = new person("Pol", "12345678B", "polcarbajalgarcia@invalid-com");

pl("Email is valid");


}catch(Exception $e){
    pe('Error: '. $e->getMessage());
}

// ------ [Bank Account 5 [Comprobacion Fraude Depostio]] ------/
pl("--------------------- Cuenta 5 [Comprobacion de Fraude Depostio] -----------------");
try{
    $bankAccount5 = new InternationalBankAccount(500);
    
    pl("Depositando 7000: Y la cuenta actual ahora esta: " . $bankAccount5->getBalance());

    $bankAccount5->transaction(new DepositTransaction(7000));
    
    pl("Depositando 19999: Y la cuenta actual ahora esta: ". $bankAccount5->getBalance());

    $bankAccount5->transaction(new DepositTransaction(19999));

    pl("Depositando 21000: Y la cuenta actual ahora esta:". $bankAccount5->getBalance());

    $bankAccount5->transaction(new DepositTransaction(21000));

}catch (Exception $e){
    pe("Error: ". $e->getMessage());
}
try{

    pl("Depositando 56000: Y la cuenta actual ahora esta: " . $bankAccount5->getBalance());

    $bankAccount5->transaction(new DepositTransaction(56000));

}catch (Exception $e){
    pe("Error: ". $e->getMessage());
}


// ------ [Bank Account 6 [Comprobacion Fraude Retiro]] ------/
pl("--------------------- Cuenta 6 [Comprobacion de Fraude Retiro] -----------------");
try{
    $bankAccount6 = new InternationalBankAccount(50000);
    
    pl("Depositando 1300: Y la cuenta actual ahora esta: " . $bankAccount6->getBalance());

    $bankAccount6->transaction(new WithdrawTransaction(1300));
    
    pl("Depositando 3400: Y la cuenta actual ahora esta: ". $bankAccount6->getBalance());

    $bankAccount6->transaction(new WithdrawTransaction(3400));

    pl("Depositando 6000: Y la cuenta actual ahora esta:". $bankAccount6->getBalance());

    $bankAccount6->transaction(new WithdrawTransaction(6000));

}catch (Exception $e){
    pe("Error: ". $e->getMessage());
}

try{

    pl("Depositando 11000: Y la cuenta actual ahora esta: " . $bankAccount5->getBalance());

    $bankAccount5->transaction(new WithdrawTransaction(11000));

}catch (Exception $e){
    pe("Error: ". $e->getMessage());
}

// ------ [Bank Account 7 [Comprobacion Api free, codigo postal] ------/
pl("--------------------- Cuenta 7 [Comprobacion de Api free, codig postal] -----------------");
try{
$bankAccount7 = new NationalBankAccount(500);

pl("Validating Email: joanrodrigues@gmail.com");

$persona3 = new person("Joan", "12345678B", "joanrodrigues@gmail.com", "08904");

pl("Email is valid");

pl("El codigo postal que ha introducido se esta validando");

pl("El codigo postal indica que usted vive en esta zona: " . $persona3->getcodigoPostal());
// ------------------------------------------------------------------
pl("Validating Email: BryanJR@gmail.com");

$persona4 = new person("Bryan", "12345678B", "BryanJR@gmail.com");

pl("Email is valid");

pl("El codigo postal que ha introducido se esta validando");

pl("El codigo postal indica que usted vive en esta zona: " . $persona4->getcodigoPostal());

}catch(Exception $e){
    pe('Error: '. $e->getMessage());
}

try{
pl("Validating Email: marcmuntane@gmail.com");

$persona5 = new person("Marc", "12345678B", "marcmuntane@gmail.com", "0000");

pl("Email is valid");

pl("El codigo postal que ha introducido se esta validando");

pl("El codigo postal indica que usted vive en esta zona: " . $persona5->getcodigoPostal());

}catch(Exception $e){
    pe('Error: '. $e->getMessage());
}
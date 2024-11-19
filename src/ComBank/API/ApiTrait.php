<?php namespace ComBank\API;

use ComBank\Transactions\Contracts\BankTransactionInterface;

trait ApiTrait 
{   

    public function validateEmail(String $email): bool{
        $IniciarApi = curl_init();
        $url = "https://disify.com/api/email/". $email;

        curl_setopt($IniciarApi, CURLOPT_URL, $url);
        curl_setopt_array($IniciarApi, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true
        ));

        $result = curl_exec($IniciarApi);
        curl_close($IniciarApi);
        $obj = json_decode($result, true);

        if($obj["format"] == true && $obj["disposable"] == false && $obj["dns"] == true){
            return true;
        }else{
            return false;
        }
    }

    public function convertBalance(float $balance): float{
        $IniciarApi = curl_init();
        $url = "https://api.fxratesapi.com/latest?base=EUR&amount=". $balance;

        curl_setopt($IniciarApi, CURLOPT_URL, $url);
        curl_setopt_array($IniciarApi, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true
        ));

        $result = curl_exec($IniciarApi);
        curl_close($IniciarApi);
        $obj = json_decode($result, true);

        return $obj["rates"]["USD"];
    }

    public function detectFraud(BankTransactionInterface $bankTransaction) {

        $IniciarApi = curl_init();
        $url = "https://673609265995834c8a952328.mockapi.io/amount/Fraude";
    
        curl_setopt($IniciarApi, CURLOPT_URL, $url);
        curl_setopt_array($IniciarApi, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true
        ));
    
        $result = curl_exec($IniciarApi);
        curl_close($IniciarApi);
        $obj = json_decode($result, true);
        $fraude = false;
        for($i = 0; $i < 8; $i++){

            if ($obj[$i]["Type-Movement"] == $bankTransaction->getTransactionInfo()) {
                if ($obj[$i]["Amount"] <= $bankTransaction->getAmount() && $obj[$i]["Alowwed"] == true) {
                    $fraude = false;
                } elseif ($obj[$i]["Amount"] <= $bankTransaction->getAmount() && $obj[$i]["Alowwed"] == false) {
                    $fraude = true;
                }
            }
        }
        return $fraude;
    }

    /* Esta funcion no le hagas caso es solo para saber a cuanto esta la conversion del Euro al dollar, para que quede mas estetico en el index */
    public function pillarConversionDollar(): float{
        $IniciarApi = curl_init();
        $url = "https://api.fxratesapi.com/latest?base=EUR&amount=1";

        curl_setopt($IniciarApi, CURLOPT_URL, $url);
        curl_setopt_array($IniciarApi, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true
        ));

        $result = curl_exec($IniciarApi);
        curl_close($IniciarApi);
        $obj = json_decode($result, true);

        return $obj["rates"]["USD"];
    }
}
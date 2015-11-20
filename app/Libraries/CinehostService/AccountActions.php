<?php 
namespace App\Libraries\CinehostService;

class AccountActions {
    private static $initialized = false;
    private static function initialize(){
        if (self::$initialized)
                return;
        self::$initialized = true;
    }
    /**
    * Return Get All Subscribers
    *
    * @return Array
    */ 
    
    public static function removeAccount($account_id) {
            
        if (empty($account_id))
            return false;
        $url = 'http://api.cinehost.com/accounts/remove?account_id='.$account_id;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json'));
       // curl_setopt($ch, CURLOPT_USERPWD, '6GryPGniSwRs11OzhWBI:x');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        if($response === false)
        {
            echo ' curl: ' . curl_error($ch);
        }
        $choutput = json_decode($response);
       
        return $choutput;
    }
}
?>
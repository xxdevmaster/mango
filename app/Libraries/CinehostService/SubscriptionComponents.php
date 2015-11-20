<?php 
namespace App\Libraries\CinehostService;

class SubscriptionComponents {
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
    
    public static function getSubscriptions() {
            $url = 'http://billing.cinehost.com/chargify/getSubscriptions';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);

            $response = curl_exec($ch);
            if($response === false)
            {
                echo ' curl: ' . curl_error($ch);
            }
            $choutput = json_decode($response);
            curl_close($ch);
            return $choutput;
    }
    public static function getAccountComponents($account_id) {
            if ($account_id>0){
                $url = 'http://billing.cinehost.com/chargify/getAccountComponents?account_id='.$account_id;
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);

                $response = curl_exec($ch);
                if($response === false)
                {
                    echo ' curl: ' . curl_error($ch);
                }
                $choutput = json_decode($response);
                curl_close($ch);
            }
            else
                return false;
            return $choutput;
    }
    public static function getAllComponents($family_id) {
        if (!$family_id){ return false;}
        $url = 'https://cinehost.chargify.com/product_families/'.$family_id.'/components.json';
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json'));
        curl_setopt($ch, CURLOPT_USERPWD, '6GryPGniSwRs11OzhWBI:x');
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
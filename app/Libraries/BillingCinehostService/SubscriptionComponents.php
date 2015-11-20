<?php 
namespace App\Libraries\BillingCinehostService;

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
            if (empty($account_id)){
                return false;
            }
            $url = 'http://billing.cinehost.com/chargify/getSubscription?account_id='.$account_id;
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
    /**
    * Return Account Payment List
    *
    * @param  int $account_id  Account id 
    * @return Array
    */ 
    public static function getAccountPaymentList($account_id) {
            if (empty($account_id)){
                return false;
            }
            $url = 'http://billing.cinehost.com/chargify/payments?account_id='.$account_id;
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
    /**
    *  Add New Credit Card to Account
    *
    * @param  int $account_id Account id
    * @param  string $first_name
    * @param  string $last_name
    * @param  string $full_number
    * @param  string $expiration_month
    * @param  string $expiration_year
    * @param  string $billing_city
    * @param  string $billing_address
    * @param  string $billing_zip
    * @param  string $billing_state
    * @return Array
    */ 
    public static function addCreditCard($account_id,$title,$first_name,$last_name,$full_number,$cvv,$expiration_month,$expiration_year,$billing_country,$billing_city,$billing_address,$billing_address_2,$billing_zip,$billing_state) {
            $url = 'http://billing.cinehost.com/chargify/addCreditCard?'
                    . 'account_id='.$account_id.''
                    . '&title='.urlencode($title).''
                    . '&first_name='.urlencode($first_name).''
                    . '&last_name='.urlencode($last_name).''
                    . '&full_number='.$full_number.''
                    . '&cvv='.$cvv.''
                    . '&expiration_month='.$expiration_month.''
                    . '&expiration_year='.$expiration_year.''
                    . '&billing_country='.urlencode($billing_country).''
                    . '&billing_city='.urlencode($billing_city).''
                    . '&billing_address='.urlencode($billing_address).''
                    . '&billing_address_2='.urlencode($billing_address_2).''
                    . '&billing_zip='.$billing_zip.''
                    . '&billing_state='.urlencode($billing_state).''
                    ;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);

            $response = curl_exec($ch);
            /*if($response === false)
            {
                return ' curl: ' . curl_error($ch);
            }*/
            $choutput = json_decode($response);
            curl_close($response);
            return $choutput;
    }
    /**
    * Update Credir Card info
    *
    * @param  int $id Credit Card Id
    * @param  string $first_name
    * @param  string $last_name
    * @param  string $expiration_month
    * @param  string $expiration_year
    * @param  string $billing_city
    * @param  string $billing_address
    * @param  string $billing_zip
    * @param  string $billing_state
    * @return bool
    */ 
    public static function updateCreditCard($id,$title,$first_name,$last_name,$expiration_month,$expiration_year,$billing_country,$billing_city,$billing_address,$billing_address_2,$billing_zip,$billing_state) {
            $url = 'http://billing.cinehost.com/chargify/updateCreditCard?'
                    . 'id='.$id.''
                    . '&title='.urlencode($title).''
                    . '&first_name='.urlencode($first_name).''
                    . '&last_name='.urlencode($last_name).''
                    . '&expiration_month='.$expiration_month.''
                    . '&expiration_year='.$expiration_year.''
                    . '&billing_country='.urlencode($billing_country).''
                    . '&billing_city='.urlencode($billing_city).''
                    . '&billing_address='.urlencode($billing_address).''
                    . '&billing_address_2='.urlencode($billing_address_2).''
                    . '&billing_zip='.$billing_zip.''
                    . '&billing_state='.urlencode($billing_state).''
                    ;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);

            $response = curl_exec($ch);
            if($response === false)
            {
               return ' curl: ' . curl_error($ch);
            }
            return $choutput;
    }
   
}
?>
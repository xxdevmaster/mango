<?php
namespace App\Libraries\CHhelper;

use DB;
use App\Film;
use App\Models\ZaccountsView;
use Auth;
class CHhelper {
	private static $allUniqueLocales = array();
	
	public static function getUniqueLocale($allLocales, $locales)
	{	
		foreach($locales as $k => $v){
			if(array_key_exists( $v->locale, $allLocales )){
				self::$allUniqueLocales[$v->locale] = $allLocales[$v->locale];
			}			
		}

		return array_diff_key($allLocales, self::$allUniqueLocales);			
	}
	
	public static function filterInput($data) 
	{
		return trim(strip_tags($data));
	}	
	
	public static function filterInputInt($data) 
	{
		//$data = mb_substr($data, 0, 11);
		return (int) trim(abs(strip_tags($data)));
	}

	public static function convertBytes($bytes, $precision = 2) {
		$units = array('B', 'KB', 'MB', 'GB');

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);

		// Uncomment one of the following alternatives
		$bytes /= pow(1024, $pow);
		// $bytes /= (1 << (10 * $pow));

		return round($bytes, $precision) . ' ' . $units[$pow];
	}

	public static function getAgeRanges($start,$end, $range ){
		$out['0,'.$start] = 'Under '.$start;
		for ($i=$start;$i<($end + 1);$i=$i+$range)
			$out[$i.','.($i+$range)] = $i.' - '.($i+$range);
		return $out;
	}

	public function countUsers(){
		$authUser = Auth::user();
		$storeID = $authUser->account->platforms_id;
		$companyID = $authUser->account->companies_id;
		if($companyID == 1)// cinehost
			$totalQuery = ZaccountsView::getUsersTotalInAuthCinehost();
		else
			$totalQuery = ZaccountsView::getUsersTotal($storeID);

		if($totalQuery->isEmpty())
			return 0;
		else
			return $totalQuery->first()->count;
	}

	/**
	 * Get all currencies.
	 * @return collection
	*/
	public static function getCurrencies()
	{
		return collect([
			'USD' => 'USD',
			'EUR' => 'EUR',
			'RUB' => 'RUB'
		]);
	}

	/**
	 * Get all Euro plans.
	 * @return collection
	 */
	public static function getEuroPlans()
	{
		return collect([
			'T1_EUR'  => ' EUR 0.99',
			'T2_EUR'  => ' EUR 1.99',
			'T3_EUR'  => ' EUR 2.99',
			'T4_EUR'  => ' EUR 3.99',
			'T5_EUR'  => ' EUR 4.99',
			'T6_EUR'  => ' EUR 5.99',
			'T7_EUR'  => ' EUR 6.99',
			'T8_EUR'  => ' EUR 7.99',
			'T9_EUR'  => ' EUR 8.99',
			'T10_EUR' => ' EUR 9.99',
			'T11_EUR' => ' EUR 10.99',
			'T12_EUR' => ' EUR 11.99',
			'T13_EUR' => ' EUR 12.99',
			'T14_EUR' => ' EUR 13.99',
			'T15_EUR '=> ' EUR 14.99'
		]);
	}

	/**
	 * Get all Euro amount.
	 * @return collection
	 */
	public static function getEuroAmount()
	{
		return collect([
			'T1_EUR'  => '0.99',
			'T2_EUR'  => '1.99',
			'T3_EUR'  => '2.99',
			'T4_EUR'  => '3.99',
			'T5_EUR'  => '4.99',
			'T6_EUR'  => '5.99',
			'T7_EUR'  => '6.99',
			'T8_EUR'  => '7.99',
			'T9_EUR'  => '8.99',
			'T10_EUR' => '9.99',
			'T11_EUR' => '10.99',
			'T12_EUR' => '11.99',
			'T13_EUR' => '12.99',
			'T14_EUR' => '13.99',
			'T15_EUR' => '14.99'
		]);
	}

	public function getTitlesTotal()
	{
		$authUser = Auth::user();
		$storeID = $authUser->account->platforms_id;
		$companyID = $authUser->account->companies_id;


		if($storeID && !$companyID){
			$q="SELECT COUNT(*) as total FROM cc_films INNER JOIN cc_base_contracts ON cc_base_contracts.films_id=cc_films.id
                INNER JOIN cc_channels_contracts ON cc_channels_contracts.bcontracts_id=cc_base_contracts.id
                WHERE cc_channels_contracts.channel_id=".$storeID." AND cc_films.deleted=0";
		}elseif(!$storeID && $companyID){
			$q="SELECT count(cc_films.id) as total FROM cc_films INNER JOIN fk_films_owners ON fk_films_owners.films_id=cc_films.id
                WHERE fk_films_owners.owner_id=".$companyID." AND fk_films_owners.type=0 AND cc_films.deleted=0";
		}else{
			$q = "SELECT COUNT(*) AS total FROM (
                        SELECT DISTINCT cc_films.id as ids FROM cc_films INNER JOIN cc_base_contracts ON cc_base_contracts.films_id=cc_films.id
                INNER JOIN cc_channels_contracts ON cc_channels_contracts.bcontracts_id=cc_base_contracts.id
                WHERE  cc_channels_contracts.channel_id=".$storeID." AND cc_films.deleted=0
                UNION SELECT DISTINCT cc_films.id as ids FROM cc_films INNER JOIN fk_films_owners ON fk_films_owners.films_id=cc_films.id
                WHERE fk_films_owners.owner_id=".$companyID." AND fk_films_owners.type=0 AND cc_films.deleted=0)  AS forTotal";
		}

		return DB::select(DB::raw($q))[0]->total;

	}
}
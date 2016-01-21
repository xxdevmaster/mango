<?php
namespace App\Libraries\CHhelper;

use DB;
use App\Film;
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
		$data = mb_substr($data, 0, 11);
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

	public static function getAccountAllTitlesCount($platformId, $companyId, $filter = null)
	{
		if($filter != null AND count($filter) >=1)
			$filter = " AND ".implode(" ", $filter);
		else
			$filter = '';

		/*

 		if (IsPermit::isPL())
            $res= G('DB')->query($q="SELECT COUNT(*) as total FROM cc_films INNER JOIN cc_base_contracts ON cc_base_contracts.films_id=cc_films.id
                INNER JOIN cc_channels_contracts ON cc_channels_contracts.bcontracts_id=cc_base_contracts.id
                WHERE 1 $filter AND cc_channels_contracts.channel_id='".$_SESSION['WL_ID']."' AND cc_films.deleted=0");
        else if (IsPermit::isCP())
        {
            $res= G('DB')->query($q="SELECT count(cc_films.id) as total FROM cc_films INNER JOIN fk_films_owners ON fk_films_owners.films_id=cc_films.id
                WHERE 1 $filter AND fk_films_owners.owner_id='".$_SESSION['STUDIO_IN']."' AND fk_films_owners.type=0 AND cc_films.deleted=0");
        }
        else if (IsPermit::isCPPL()){
        $q = "SELECT COUNT(*) AS total FROM (
                        SELECT DISTINCT cc_films.id as ids FROM cc_films INNER JOIN cc_base_contracts ON cc_base_contracts.films_id=cc_films.id
                INNER JOIN cc_channels_contracts ON cc_channels_contracts.bcontracts_id=cc_base_contracts.id
                WHERE  cc_channels_contracts.channel_id='".$_SESSION['WL_ID']."' AND cc_films.deleted=0
                UNION SELECT DISTINCT cc_films.id as ids FROM cc_films INNER JOIN fk_films_owners ON fk_films_owners.films_id=cc_films.id
                WHERE fk_films_owners.owner_id='".$_SESSION['STUDIO_IN']."' AND fk_films_owners.type=0 AND cc_films.deleted=0 )  AS forTotal";
                $res= G('DB')->query($q);
        }*/

		$q = "SELECT COUNT(*) AS total FROM (
                        SELECT DISTINCT cc_films.id as ids FROM cc_films INNER JOIN cc_base_contracts ON cc_base_contracts.films_id=cc_films.id
                INNER JOIN cc_channels_contracts ON cc_channels_contracts.bcontracts_id=cc_base_contracts.id
                WHERE  cc_channels_contracts.channel_id=".$platformId." AND cc_films.deleted=0".$filter."
                UNION SELECT DISTINCT cc_films.id as ids FROM cc_films INNER JOIN fk_films_owners ON fk_films_owners.films_id=cc_films.id
                WHERE fk_films_owners.owner_id=".$companyId." AND fk_films_owners.type=0 AND cc_films.deleted=0".$filter." )  AS forTotal";
		return DB::select(DB::raw($q));

	}

	public static function getAccountAllTitles($platformId, $companyId, $filter = null, $limit = null, $offset = null)
	{
		if($filter != null AND count($filter) >=1)
			$filter = " AND ".implode(" ", $filter);
		else
			$filter = '';

		if($limit != null)
			$limit = ' LIMIT '.$limit;
		else
			$limit = '';

		if($offset != null)
			$offset = ' OFFSET '.$offset;
		else
			$offset = '';

		/* if (IsPermit::isPL())
           $res= G('DB')->query($q="SELECT COUNT(*) as total FROM cc_films INNER JOIN cc_base_contracts ON cc_base_contracts.films_id=cc_films.id
               INNER JOIN cc_channels_contracts ON cc_channels_contracts.bcontracts_id=cc_base_contracts.id
               WHERE 1 $filter AND cc_channels_contracts.channel_id='".$_SESSION['WL_ID']."' AND cc_films.deleted=0");
       else if (IsPermit::isCP())
       {
           $res= G('DB')->query($q="SELECT count(cc_films.id) as total FROM cc_films INNER JOIN fk_films_owners ON fk_films_owners.films_id=cc_films.id
               WHERE 1 $filter AND fk_films_owners.owner_id='".$_SESSION['STUDIO_IN']."' AND fk_films_owners.type=0 AND cc_films.deleted=0");
       }
       else if (IsPermit::isCPPL()){
       $q = "SELECT COUNT(*) AS total FROM (
                       SELECT DISTINCT cc_films.id as ids FROM cc_films INNER JOIN cc_base_contracts ON cc_base_contracts.films_id=cc_films.id
               INNER JOIN cc_channels_contracts ON cc_channels_contracts.bcontracts_id=cc_base_contracts.id
               WHERE  cc_channels_contracts.channel_id='".$_SESSION['WL_ID']."' AND cc_films.deleted=0
               UNION SELECT DISTINCT cc_films.id as ids FROM cc_films INNER JOIN fk_films_owners ON fk_films_owners.films_id=cc_films.id
               WHERE fk_films_owners.owner_id='".$_SESSION['STUDIO_IN']."' AND fk_films_owners.type=0 AND cc_films.deleted=0 )  AS forTotal";
               $res= G('DB')->query($q);
       }*/

		$q = "SELECT DISTINCT cc_films.* FROM cc_films INNER JOIN cc_base_contracts ON cc_base_contracts.films_id=cc_films.id
                INNER JOIN cc_channels_contracts ON cc_channels_contracts.bcontracts_id=cc_base_contracts.id
                WHERE  cc_channels_contracts.channel_id=".$platformId." AND cc_films.deleted=0".$filter."
                UNION SELECT DISTINCT cc_films.* FROM cc_films INNER JOIN fk_films_owners ON fk_films_owners.films_id=cc_films.id
                WHERE fk_films_owners.owner_id=".$companyId." AND fk_films_owners.type=0 AND cc_films.deleted=0".$filter." ".$limit." ".$offset."";
		return Film::hydrateRaw($q);
		//return Film::selectRaw("");

		//return DB::select(DB::raw($q));

	}
}
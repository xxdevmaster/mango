<?php
namespace App\Libraries\CHhelper;

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
		return trim((int)abs($data));
	}
}
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    protected $table = "cc_films";

    public $timestamps = false;
   
    public function companies(){
        return $this->belongsToMany('App\Company', 'fk_films_owners', 'films_id', 'owner_id');
    }

    public function baseContract(){
        return $this->hasOne('App\BaseContracts', 'films_id');
    }

    public function filmOwners()
    {
        return $this->hasOne('App\FilmOwners', 'films_id');
    }

	public function genres(){
        return $this->belongsToMany('App\Genres', 'fk_films_genres', 'films_id', 'genres_id');
    }	
	public function locales(){
        return $this->hasMany('App\LocaleFilms', 'films_id');
    }

	public function languages(){
        return $this->belongsToMany('App\Languages', 'fk_films_languages', 'films_id', 'languages_id');
    }	
	
	public function prodCompanies(){
        return $this->belongsToMany('App\ProdCompanies', 'fk_films_prodcompanies', 'films_id', 'prodcompanies_id');
    }
	
	public function countries(){
        return $this->belongsToMany('App\Countries', 'fk_films_countries', 'films_id', 'countries_id');
    }
	public function geoCountries(){
        return $this->belongsToMany('App\Countries', 'cc_geo_contracts', 'films_id', 'countries_id')->where('cc_countries.deleted','0')->select('cc_geo_contracts.*','cc_countries.title', 'cc_countries.currency_code');
    }

	public function persons(){
       return $this->belongsToMany('App\Models\Persons', 'fk_films_persons', 'films_id', 'persons_id')->join('cc_jobs', 'cc_jobs.id', '=', 'fk_films_persons.jobs_id')->where('cc_persons.deleted', '0')->select(array('cc_jobs.*', 'cc_jobs.title as jobs_title', 'cc_persons.*'));
    }

	public function jobs(){
        return $this->belongsToMany('App\Models\Jobs', 'fk_films_persons', 'films_id', 'jobs_id');
    }

	public function medias(){
        return $this->hasMany('App\Models\FilmsMedia', 'films_id');
    }

    /*public static function getAccountAllTitles($platformID, $companyID, $select = 'cc_films.*', $filter, $orderBy = false, $orderType = 'ASC', $limit = null, $offset = null)
    {
       if($filter != '')
            $filter = implode(" ", $filter);

        if($select != 'cc_films.*')
            $select = implode(",", $select);
        $orderBy = (!empty($orderBy)) ? ' ORDER BY '.$orderBy.' '.$orderType : ' ORDER BY id '.$orderBy;

        if($limit != null)
            $limit = ' LIMIT '.$limit;
        else
            $limit = '';

        if($offset != null)
            $offset = ' OFFSET '.$offset;
        else
            $offset = '';

        if($platformID > 0 && $companyID > 0){
            $q = "SELECT DISTINCT $select FROM cc_films INNER JOIN cc_base_contracts ON cc_base_contracts.films_id=cc_films.id
                INNER JOIN cc_channels_contracts ON cc_channels_contracts.bcontracts_id=cc_base_contracts.id
                WHERE  cc_channels_contracts.channel_id=".$platformID." AND cc_films.deleted=0 ".$filter."
                UNION SELECT DISTINCT $select FROM cc_films INNER JOIN fk_films_owners ON fk_films_owners.films_id=cc_films.id
                WHERE fk_films_owners.owner_id=".$companyID." AND fk_films_owners.type=0 AND cc_films.deleted=0 $filter $orderBy $limit $offset ";
        }elseif($platformID > 0){
            $q = "SELECT $select FROM cc_films INNER JOIN cc_base_contracts ON cc_base_contracts.films_id=cc_films.id
                INNER JOIN cc_channels_contracts ON cc_channels_contracts.bcontracts_id=cc_base_contracts.id
                WHERE  cc_channels_contracts.channel_id=".$platformID." AND cc_films.deleted=0 ".$filter.' '.$orderBy;
        }elseif($companyID > 0){
            $q = "SELECT $select FROM cc_films INNER JOIN fk_films_owners ON fk_films_owners.films_id=cc_films.id
                WHERE fk_films_owners.owner_id=".$companyID." AND fk_films_owners.type=0 AND cc_films.deleted=0 $filter $orderBy $limit $offset ";
        }

        return self::hydrateRaw($q);
    }

    public static function getAccountAllTitlesCount($platformID, $companyID, $filter)
    {
        if($filter != '')
            $filter = implode(" ", $filter);

        if($platformID && !$companyID){
            $q="SELECT COUNT(*) as total FROM cc_films INNER JOIN cc_base_contracts ON cc_base_contracts.films_id=cc_films.id
                INNER JOIN cc_channels_contracts ON cc_channels_contracts.bcontracts_id=cc_base_contracts.id
                WHERE cc_channels_contracts.channel_id=".$platformID." AND cc_films.deleted=0 ".$filter;
        }elseif(!$platformID && $companyID){
            $q="SELECT count(cc_films.id) as total FROM cc_films INNER JOIN fk_films_owners ON fk_films_owners.films_id=cc_films.id
                WHERE fk_films_owners.owner_id=".$companyID." AND fk_films_owners.type=0 AND cc_films.deleted=0 ".$filter;
        }else{
            $q = "SELECT COUNT(*) AS total FROM (
                        SELECT DISTINCT cc_films.id as ids FROM cc_films INNER JOIN cc_base_contracts ON cc_base_contracts.films_id=cc_films.id
                INNER JOIN cc_channels_contracts ON cc_channels_contracts.bcontracts_id=cc_base_contracts.id
                WHERE  cc_channels_contracts.channel_id=".$platformID." AND cc_films.deleted=0 ".$filter."
                UNION SELECT DISTINCT cc_films.id as ids FROM cc_films INNER JOIN fk_films_owners ON fk_films_owners.films_id=cc_films.id
                WHERE fk_films_owners.owner_id=".$companyID." AND fk_films_owners.type=0 AND cc_films.deleted=0 ".$filter." )  AS forTotal";
        }

        return self::hydrateRaw($q);
    }*/

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Company extends Model
{
    //
    protected $table = "cc_companies";

    public $timestamps = false;

    protected $fillable = ['id', 'title', 'logo', 'brief', 'website', 'phone', 'person', 'email', 'address', 'founded', 'deleted', 'agreement'];
/*
    public function filmOwners(){
        return $this->hasMany('App\FilmOwners', 'owner_id');
    }*/


    public function films(){
        return $this->belongsToMany('App\Film', 'fk_films_owners', 'owner_id', 'films_id')->where('cc_films.deleted', '0');
    }

    public function stores(){
        return $this->belongsToMany('App\Film', 'fk_films_owners', 'owner_id', 'films_id');
    }

    public function vaults(){
        return $this->hasMany('App\Models\Vaults', 'companies_id');
    }

    public static function getXchangeContentProviders($condition = "", $limit = 20 , $offset = 0)
    {
        $queryString ="
            SELECT cc_companies.id,cc_companies.title,cc_companies.logo,cc_companies.website FROM cc_companies
                INNER JOIN cc_vaults ON cc_companies.id = cc_vaults.companies_id
                    WHERE cc_companies.title<>'' AND cc_companies.id>1 AND
                      (
                        SELECT COUNT(cc_films.id) FROM cc_films
                            INNER JOIN fk_films_owners ON cc_films.id = fk_films_owners.films_id
                            WHERE cc_films.deleted = 0 AND fk_films_owners.owner_id=cc_companies.id  AND type=0
                      )>0 $condition GROUP BY cc_companies.id LIMIT ".$limit." OFFSET ".$offset
        ;

        return self::hydrateRaw($queryString);
    }

    public static function getXchangeContentProvidersCountAll($condition = "")
    {
        $queryString ="
            SELECT COUNT(cnt.id) as count FROM
                (
                    SELECT cc_companies.id FROM cc_companies  INNER JOIN cc_vaults ON cc_companies.id = cc_vaults.companies_id
                    WHERE cc_companies.title<>'' AND cc_companies.id>1 AND
                    (
                       SELECT COUNT(cc_films.id) FROM cc_films
                       INNER JOIN fk_films_owners ON cc_films.id = fk_films_owners.films_id
                       WHERE cc_films.deleted = 0 AND fk_films_owners.owner_id=cc_companies.id  AND type=0
                    )>0 $condition  GROUP BY cc_companies.id
                ) as cnt";

        return self::hydrateRaw($queryString);
    }

    public function companyVaultsFilms($limit = 20, $offset = 0){
        return $this->belongsToMany('App\Film', 'cc_vaults', 'companies_id', 'films_id')
                    ->where('cc_films.deleted', '0')
                    ->select('cc_films.id', 'cc_films.title', 'cc_films.cover', 'cc_vaults.id as vaultID')
                    ->limit($limit)->skip($offset);
    }

    public function companyVaultsFilmsCountAll(){
        return $this->belongsToMany('App\Film', 'cc_vaults', 'companies_id', 'films_id')
                    ->where('cc_films.deleted', '0')
                    ->select(DB::raw('COUNT(cc_films.id) as count'));
    }
}

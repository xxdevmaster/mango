<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Store extends Model
{
    protected $table = "cc_channels";

    public $timestamps = false;

    protected $fillable = [
        'id', 'title', 'position', 'logo',
        'slogo', 'brief', 'deleted', 'visible',
        'genres_visible', 'countries_visible', 'slider',
        'frontpage', 'css', 'rawhtml', 'fbpage', 'links', 'appid',
        'playertheme', 'bgimg', 'welcome_message', 'html_header', 'hash', 'trailer_bucket', 'bonus_bucket',
        'movie_bucket', 'aws_secretkey', 'aws_accesskey', 'parent', 'slider_size', 'secret_key', 'aws_endpoint',
        'website', 'phone', 'person', 'email', 'address', 'legal_type', 'titles_amnt', 'category'
    ];

    public function contracts(){
        return $this->belongsToMany('App\BaseContracts', 'cc_channels_contracts', 'channel_id', 'bcontracts_id');
    }

    public function storesFilmsCount(){
        return $this->belongsToMany('App\Film', 'fk_films_owners', 'owner_id', 'films_id')->select(DB::raw('COUNT(*) as count'));
    }

    public function storesFilms($limit = 20, $offset = 0){
        $query = $this->belongsToMany('App\Film', 'fk_films_owners', 'owner_id', 'films_id')->where('cc_films.deleted', '0')->where('fk_films_owners.type', '1');
        if($limit)
            return $query->limit($limit)->skip($offset);
        else
            return $query;
    }

    /**
     * Get store films in partner stores.
     * @param array or collection $whereIn
     * @return integer
     */
    public function storeFilmsCount($whereIn)
    {
        return $this->belongsToMany('App\Film', 'fk_films_owners', 'owner_id', 'films_id')->whereIn('cc_films.id', $whereIn)->count();
    }

    /**
     * Get store films in partner stores.
     *  @param array or collection $whereIn
     *  @param integer $limit
     *  @param integer $offset
     * @return collection
     */
    public function storeFilms($whereIn = [], $limit = 20, $offset = 0)
    {
        return $this->belongsToMany('App\Film', 'fk_films_owners', 'owner_id', 'films_id')->whereIn('cc_films.id', $whereIn)->where('cc_films.deleted', '0')->where('fk_films_owners.type', '1')->limit($limit)->skip($offset);
    }

    public static function getFilmStores($platform_ID, $company_ID)
    {
        if($company_ID == 1){
            $idsQuery = "SELECT id FROM cc_films WHERE deleted=0";
        }
        if($platform_ID > 0 && $company_ID > 0){
            $idsQuery = "SELECT ids as id  FROM (
                        SELECT DISTINCT cc_films.id as ids FROM cc_films INNER JOIN cc_base_contracts ON cc_base_contracts.films_id=cc_films.id
                        INNER JOIN cc_channels_contracts ON cc_channels_contracts.bcontracts_id=cc_base_contracts.id
                        WHERE  cc_channels_contracts.channel_id='".$platform_ID."' AND cc_films.deleted=0 UNION
                        SELECT DISTINCT cc_films.id as ids FROM cc_films INNER JOIN fk_films_owners ON fk_films_owners.films_id=cc_films.id
                        WHERE fk_films_owners.owner_id='".$company_ID."' AND fk_films_owners.type=0 AND cc_films.deleted=0
                )  AS id";
        }elseif($platform_ID > 0){
            $idsQuery = "SELECT cc_films.id FROM cc_films INNER JOIN cc_base_contracts ON cc_base_contracts.films_id=cc_films.id
                    INNER JOIN cc_channels_contracts ON cc_channels_contracts.bcontracts_id=cc_base_contracts.id
                    WHERE cc_channels_contracts.channel_id='".$platform_ID."' AND cc_films.deleted=0";
        }elseif($company_ID > 0){
            $idsQuery = "SELECT cc_films.id FROM cc_films INNER JOIN fk_films_owners ON fk_films_owners.films_id=cc_films.id
                    WHERE fk_films_owners.owner_id='".$company_ID."' AND fk_films_owners.type=0 AND cc_films.deleted=0";
        }

		
        $q ="SELECT cc_channels.id,cc_channels.title FROM cc_channels JOIN fk_films_owners ON cc_channels.id=fk_films_owners.owner_id
             WHERE fk_films_owners.films_id IN ($idsQuery) AND fk_films_owners.type=1 AND cc_channels.title<>'' GROUP BY cc_channels.id ";

			 
        return self::hydrateRaw($q);
    }

    public static function getXchangeStores($limit, $offset, $like = [])
    {
        $conditions = '';
        if (count($like) > 0)
        {
            $conditions .= " AND ";
            $conditions .= implode("\n", $like);
        }		

        if($limit != null)
            $limit = ' LIMIT '.$limit;
        else
            $limit = '';

        if($offset != null)
            $offset = ' OFFSET '.$offset;
        else
            $offset = '';
		
        $queryString ="
			SELECT 
				cc_channels.id,cc_channels.title,cc_channels.logo,cc_channels.website , cc_channels.brief 
			FROM cc_channels WHERE  cc_channels.title<>'' $conditions AND
            (
                 SELECT COUNT(cc_films.id) FROM cc_films
                    INNER JOIN fk_films_owners ON cc_films.id = fk_films_owners.films_id
                 WHERE cc_films.deleted = 0 AND fk_films_owners.owner_id=cc_channels.id AND type=1
             )>0 ".$limit." ".$offset
        ;

        return self::hydrateRaw($queryString);
    }

    public static function getXchangeStoresCountAll($like = [])
    {
        $conditions = '';
        if (count($like) > 0)
        {
            $conditions .= " AND ";
            $conditions .= implode("\n", $like);
        }

        $queryString = "
			SELECT COUNT(*) as total 
				FROM cc_channels WHERE cc_channels.title <> '' $conditions AND
				   (
						 SELECT COUNT(cc_films.id) FROM cc_films
							INNER JOIN fk_films_owners ON cc_films.id = fk_films_owners.films_id
						 WHERE cc_films.deleted = 0 AND fk_films_owners.owner_id=cc_channels.id AND type = '1'
					) > 0";
        return self::hydrateRaw($queryString);
    }


    /*public static function getPartnerStores($limit, $offset, $like = [])
    {
        $conditions = '';
        if (count($like) > 0)
        {
            $conditions .= " AND ";
            $conditions .= implode("\n", $like);
        }

        if($limit != null)
            $limit = ' LIMIT '.$limit;
        else
            $limit = '';

        if($offset != null)
            $offset = ' OFFSET '.$offset;
        else
            $offset = '';

        $queryString ="
			SELECT
				cc_channels.id,cc_channels.title,cc_channels.logo,cc_channels.website , cc_channels.brief
			FROM cc_channels WHERE  cc_channels.title<>'' $conditions AND
            (
                 SELECT COUNT(cc_films.id) FROM cc_films
                    INNER JOIN fk_films_owners ON cc_films.id = fk_films_owners.films_id
                 WHERE cc_films.deleted = 0 AND fk_films_owners.owner_id=cc_channels.id AND type=1
             )>0 ".$limit." ".$offset
        ;

        return self::hydrateRaw($queryString);
    }

    public static function getPartnerStoresCountAll($like = [])
    {
        $conditions = '';
        if (count($like) > 0)
        {
            $conditions .= " AND ";
            $conditions .= implode("\n", $like);
        }

        $queryString = "
			SELECT COUNT(*) as total
				FROM cc_channels WHERE cc_channels.title <> '' $conditions AND
				   (
						 SELECT COUNT(cc_films.id) FROM cc_films
							INNER JOIN fk_films_owners ON cc_films.id = fk_films_owners.films_id
						 WHERE cc_films.deleted = 0 AND fk_films_owners.owner_id=cc_channels.id AND type = '1'
					) > 0";
        return self::hydrateRaw($queryString);
    }*/
}

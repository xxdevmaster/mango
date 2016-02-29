<?php

namespace App\Http\Controllers\Xchange;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Pagination\LengthAwarePaginator;
use Auth;
use App\Libraries\CHhelper\CHhelper;
use App\Store;
use App\Film;
use App\Models\CronJobs;
use DB;
use App\Models\Vaults;
use App\Models\ChannelsFilms;
use App\Models\ChannelsContracts;
use App\Models\FilmSlidersImages;
use App\Models\CollectionsFilms;
use App\Models\Collections;
use App\Models\SubchannelsFilms;
use App\Models\Subchannels;
use App\BaseContracts;
use App\FilmOwners;

class XchangeTitlesController extends Controller
{
    private $request;

    private $authUser;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->authUser = Auth::user();
        $this->platformsID = $this->authUser->account->platforms_id;
        $this->companiesID = $this->authUser->account->companies_id;
    }

    public function xchangeTitlesShow()
    {
        $this->getData(0,['order' => 'id', 'ordertype' => '', 'vaultStatus' => '']);
        //dd($this->authUser->account->store->contracts);
        $films = collect();

        foreach($this->authUser->account->store->contracts as $val){
            $films[] =  $val->films;
        }
        //dd($films);
        //dd(Vaults::all());
        //$cp = Store::getFilmStores($this->platformsID, $this->companiesID);
        if(count($films) != 0)
            $cp = $films->first()->companies->keyBy('id');
        else
            $cp = [];
        $films = Film::getAccountAllTitles($this->platformsID, 0)->take(10)->keyBy('id');
        //dd($films);
        //dd($companies);
        $paginator = new LengthAwarePaginator($films, count($films), 20, 0);
        return view('xchange.xchangeTitles.xchangeTitles', compact('films', 'cp', 'paginator'));
    }

    public function getVaultAllFilms(){

        $vaults = Vaults::all();

        return $vaults;
    }

    public function getOwnerFilms()
    {
        $row = DB::table('cc_films')->select('cc_films.id')
            ->join('fk_films_owners', 'fk_films_owners.films_id', '=', 'cc_films.id')
            ->join('cc_vaults', 'cc_vaults.films_id', '=', 'cc_films.id')
            ->where('fk_films_owners.owner_id', $this->companiesID)
            ->orWhere('fk_films_owners.owner_id', $this->platformsID)
            ->where('fk_films_owners.type', '0')
            ->orWhere('fk_films_owners.type', '1')
            ->where('fk_films_owners.role', '<', '2')
            ->get();
        $films = array();
        foreach($row as $val){
            array_push($films, $val->id);
        }

        //dd(Film::whereIn('id', $films)->get());
        return $films;
    }

    public function getData($pageIndex,$filterArray=''){
        //$this->classObj = new stdClass();

        $filter_cp = '';
        $filter_pl = '';
        $filter = '';
        $sort = " cc_films.".$filterArray['order']." ".$filterArray['ordertype']." ";
        if (!empty($filterArray['search_word'])) {
            $filter = " AND (cc_films.title LIKE '%".$filterArray['search_word']."%')";
        }
        if (!empty($filterArray['status'])) {
            $filter .= " AND cc_films.published='".$filterArray['status']."' ";
        }
        if (!empty($filterArray['pl'])) {
            $plFilms = array();
            $resPL= G('DB')->query($q="SELECT films_id FROM fk_films_owners WHERE owner_id='".$filterArray['pl']."'");
            while($rowPL = $resPL->fetch(PDO::FETCH_ASSOC)){
                array_push($plFilms,$rowPL['films_id']);
            }
            $filter_pl .= " AND cc_films.id IN  ('".implode("','",$plFilms)."') ";
        }
        if (!empty($filterArray['cp'])) {
            $cpFilms = array();
            $resCP= G('DB')->query($q="SELECT films_id FROM fk_films_owners WHERE owner_id='".$filterArray['cp']."'");
            while($rowCP = $resCP->fetch(PDO::FETCH_ASSOC)){
                array_push($cpFilms,$rowCP['films_id']);
            }
            $filter_cp .= " AND cc_films.id IN  ('".implode("','",$cpFilms)."') ";
        }
        if (!empty($filterArray['pl']) && !empty($filterArray['cp'])){
            $filterFilms =  array_intersect($plFilms,$cpFilms);
            $filter .=  " AND cc_films.id IN  ('".implode("','",$filterFilms)."') ";
        }
        else
            $filter .=  $filter_pl.$filter_cp;

        /**/
        if ($filterArray['vaultStatus']==1) {
            $pre_query = $pre_query = "INNER JOIN cc_channels_contracts ON cc_channels_contracts.bcontracts_id=cc_base_contracts.id AND cc_channels_contracts.channel_id=".$this->platformsID."
                WHERE 1=1 $filter_cp $filter  AND cc_films.deleted=0 ";
        }
        else if ($filterArray['vaultStatus']==2) {
            $pre_query = "LEFT OUTER JOIN cc_channels_contracts ON cc_channels_contracts.bcontracts_id=cc_base_contracts.id AND cc_channels_contracts.channel_id=".$this->platformsID."
                WHERE 1=1  $filter AND cc_channels_contracts.id IS NULL   AND cc_films.deleted=0";
        }
        else  {
            $pre_query = "LEFT OUTER JOIN cc_channels_contracts ON cc_channels_contracts.bcontracts_id=cc_base_contracts.id AND cc_channels_contracts.channel_id=".$this->platformsID."
                WHERE 1=1 $filter  AND cc_films.deleted=0";
        }
        $q="Select cc_films.*,cc_films.id AS film_id,cc_vaults.*,cc_vaults.id AS VID, cc_channels_contracts.id AS PLConn  FROM cc_vaults
                    INNER JOIN cc_films ON cc_films.id=cc_vaults.films_id
                    INNER JOIN cc_base_contracts ON cc_base_contracts.films_id=cc_films.id
                    $pre_query GROUP BY cc_vaults.films_id ORDER BY $sort ";

        $res = Film::hydrateRaw($q)->keyBy('id');

        $qt="Select COUNT(cc_films.id)as total  FROM cc_vaults
                    INNER JOIN cc_films ON cc_films.id=cc_vaults.films_id
                    INNER JOIN cc_base_contracts ON cc_base_contracts.films_id=cc_films.id
                    $pre_query ";

        $resTotal = Film::hydrateRaw($qt);

        $resTotal = $resTotal->first()->total;
       // $resTotal= G('DB')->query($qt);
        //dd($res);



       // $res_deleted = G('DB')->query("SELECT cc_vaults.films_id, cc_vaults.companies_id, cc_vaults.delete_dt  FROM cron_jobs inner join cc_vaults on cron_jobs.films_id=cc_vaults.films_id");
        $res_deleted = CronJobs::join('cc_vaults', 'cron_jobs.films_id', '=', 'cc_vaults.films_id');
        //dd($res_deleted->get());
        /*while($row_deleted = $res_deleted->fetch(PDO::FETCH_ASSOC)){

            if ($row_deleted['delete_dt']>0)
                $filmsDelete['AllTeritories'][$row_deleted['films_id']]= $row_deleted['delete_dt'];
            else
                $filmsDelete['InSomeTeritories'][$row_deleted['films_id']]=1;
        }
        $rowTotal = $resTotal->fetch(PDO::FETCH_ASSOC);
        $resCnt = $rowTotal['total'];
        $this->classObj->total = $resCnt;

        $films = $this->getVaultAllFilms();
        $this->classObj->pls = $this->getFilmsPlatforms($films);
        $this->classObj->ownerFilms = $this->getOwnerFilms();
        $this->classObj->cps = $this->getFilmsContentProviders($films);
        $this->classObj->filmsDelete =$filmsDelete;*/
    }

    /**
     *@POST("/xchange/soloActAddToStore")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function soloActAddToStore()
    {
		$baseContracts = $this->request->film->baseContract;
		
		$newChannelFilm = ChannelsFilms::create([
			'channels_id' => $this->platformsID ,
			'films_id' => $this->request->filmId
		]);
		
		$newFilmOwner = FilmOwners::create([
			'owner_id' => $this->platformsID ,
			'films_id' => $this->request->filmId ,
			'type' => '1',
			'role' => '4'
		]);

		$newChannelContract = ChannelsContracts::create([
			'channel_id' => $this->platformsID ,
			'bcontracts_id' => $baseContracts->id ,
			'film_status' => '1'
		]);
		
		$vaults = Vaults::where('films_id', $this->request->filmId)->get()->keyBy('id');
		
		foreach($vaults as $key => $val){
			ChannelsVaults::create([
				'vaults_id' => $key,
				'channels_id' => $this->platformsID
			]);			
		}
    }    
	
	/**
     *@POST("/xchange/soloActDeleteFromStore")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function soloActDeleteFromStore()
    {
		$baseContracts = $this->request->film->baseContract;
		
		ChannelsFilms::where('channels_id', $this->platformsID)->where('films_id', $this->request->filmId)->delete();		
		FilmOwners::where('owner_id', $this->platformsID)->where('films_id', $this->request->filmId)->where('type', '1')->delete();
		ChannelsContracts::where('channel_id', $this->platformsID)->where('bcontracts_id', $baseContracts->id)->delete();
		
		$sildersID = Sliders::where('channel_id', $this->platformsID)->select('id')->get()->keyBy('id')->toArray();
		FilmSlidersImages::where('films_id', $this->request->filmId)
						->whereIn('sliders_id', $sildersID)
						->update([
							'films_id' => ''
						]);
		
		$collectionsID = Collections::where('channels_id', $this->platformsID)->select('id')->get()->keyBy('id')->toArray();
		CollectionsFilms::where('films_id', $this->request->filmId)
							->whereIn('collections_id', $collectionsID)
							->delete();
		
		$subchannelsID = Subchannels::where('channels_id', $this->platformsID)->select('id')->get()->keyBy('id')->toArray();
		SubchannelsFilms::where('films_id', $this->request->filmId)
							->whereIn('subchannels_id', $subchannelsID);
		
		$vaults = Vaults::where('films_id', $this->request->filmId)->get()->keyBy('id');

		foreach($vaults as $key => $val){
			ChannelsVaults::where('channels_id', $this->platformsID)->where('vaults_id', $key)->delete();		
		}	
    }
	
	/**
     *@POST("/xchange/bulkActAddToStore")
     * @Middleware("auth")
     */	
    public function bulkActAddToStore()
	{
		if(!empty($this->request->Input('filmsNotInMyStore'))){
			foreach($this->request->Input('filmsNotInMyStore') AS $k =>$v){
				if ($v == 'on'){
					$filmID = CHhelper::filterInputInt($k);
					
					$baseContracts = Film::where('id', $filmID)->first()->baseContract;
					
					$newChannelFilm = ChannelsFilms::create([
						'channels_id' => $this->platformsID ,
						'films_id' => $filmID
					]);
					
					$newFilmOwner = FilmOwners::create([
						'owner_id' => $this->platformsID ,
						'films_id' => $filmID ,
						'type' => '1',
						'role' => '4'
					]);

					$newChannelContract = ChannelsContracts::create([
						'channel_id' => $this->platformsID ,
						'bcontracts_id' => $baseContracts->id ,
						'film_status' => '1'
					]);
					
					$vaults = Vaults::where('films_id', $filmID)->get()->keyBy('id');
					
					foreach($vaults as $key => $val){
						ChannelsVaults::create([
							'vaults_id' => $key,
							'channels_id' => $this->platformsID
						]);			
					}					
				}			
			}		
		}
    }

	/**
     *@POST("/xchange/bulkActDeleteFromStore")
     * @Middleware("auth")
     */		
    public function bulkActDeleteFromStore()
	{
		if(!empty($this->request->Input('filmsInMyStore'))){
			foreach($this->request->Input('filmsInMyStore') AS $k =>$v){
				if ($v == 'on'){
					$filmID = CHhelper::filterInputInt($k);
					
					$baseContracts = Film::where('id', $filmID)->first()->baseContract;
					
					ChannelsFilms::where('channels_id', $this->platformsID)->where('films_id', $filmID)->delete();		
					FilmOwners::where('owner_id', $this->platformsID)->where('films_id', $filmID)->where('type', '1')->delete();
					ChannelsContracts::where('channel_id', $this->platformsID)->where('bcontracts_id', $baseContracts->id)->delete();
					
					$sildersID = Sliders::where('channel_id', $this->platformsID)->select('id')->get()->keyBy('id')->toArray();
					FilmSlidersImages::where('films_id', $filmID)
									->whereIn('sliders_id', $sildersID)
									->update([
										'films_id' => ''
									]);
					
					$collectionsID = Collections::where('channels_id', $this->platformsID)->select('id')->get()->keyBy('id')->toArray();
					CollectionsFilms::where('films_id', $filmID)
										->whereIn('collections_id', $collectionsID)
										->delete();
					
					$subchannelsID = Subchannels::where('channels_id', $this->platformsID)->select('id')->get()->keyBy('id')->toArray();
					SubchannelsFilms::where('films_id', $filmID)
										->whereIn('subchannels_id', $subchannelsID);
					
					$vaults = Vaults::where('films_id', $filmID)->get()->keyBy('id');

					foreach($vaults as $key => $val){
						ChannelsVaults::where('channels_id', $this->platformsID)->where('vaults_id', $key)->delete();		
					}					
				}			
			}		
		}
    }	

}

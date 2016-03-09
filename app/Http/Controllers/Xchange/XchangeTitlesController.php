<?php

namespace App\Http\Controllers\Xchange;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Pagination\LengthAwarePaginator;
use Auth;
use App\Libraries\CHhelper\CHhelper;
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
use App\FilmOwners;
use App\Company;
use App\Store;

class XchangeTitlesController extends Controller
{
    private $request;

    private $authUser;

    private $storeID;

    private $companyID;

    private $limit = 20;

    private $offset = 0;

    private $page = 0;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->authUser = Auth::user();
        $this->storeID = $this->authUser->account->platforms_id;
        $this->companyID = $this->authUser->account->companies_id;
    }

    public function xchangeTitlesShow()
    {
        $companies = $this->getContentProviders();

        return view('xchange.xchangeTitles.xchangeTitles', compact('companies'), $this->getData());
    }

    private function getContentProviders() {
        return Company::where('cc_companies.title', '<>', '')
                        ->join('cc_vaults', 'cc_companies.id', '=', 'cc_vaults.companies_id')
                        ->select('cc_companies.id', 'cc_companies.title')
                        ->groupBy('cc_companies.id')->get()->keyBy('id');
    }

    private function getVaultAllFilms()
    {
        return Vaults::lists('films_id')->toArray();
    }

    public function getOwnerFilms()
    {
        return Film::select('cc_films.id')
            ->join('fk_films_owners', 'fk_films_owners.films_id', '=', 'cc_films.id')
            ->join('cc_vaults', 'cc_vaults.films_id', '=', 'cc_films.id')
            ->where('fk_films_owners.owner_id', $this->companyID)
            ->orWhere('fk_films_owners.owner_id', $this->storeID)
            ->where('fk_films_owners.type', '0')
            ->orWhere('fk_films_owners.type', '1')
            ->where('fk_films_owners.role', '<', '2')
            ->get()->lists('id');
    }

    public function getData(){
        //$this->classObj = new stdClass();

        $filterCp = '';
        $filterPl = '';
        $filter = '';

        $order = 'title';
        $orderType = 'ASC';

        $filterArray = (!empty($this->request->Input('filter')) && is_array($this->request->Input('filter'))) ? $this->request->Input('filter') : false;

        if($filterArray) {

        }

        if($filterArray['order'])
            $order = CHhelper::filterInput($filterArray['order']);
        if($filterArray['ordertype'])
            $orderType = CHhelper::filterInput($filterArray['ordertype']);

        $sort = " cc_films.".$order." ".$orderType." ";

        if (!empty($filterArray['searchWord'])) {
            $filter = " AND (cc_films.title LIKE '".CHhelper::filterInput($filterArray['search_word'])."%' OR cc_films.id LIKE '".CHhelper::filterInput($filterArray['search_word'])."%')";
        }
        /*if (!empty($filterArray['status'])) {
            $filter .= " AND cc_films.published='".$filterArray['status']."' ";
        }*/
        if (!empty($filterArray['pl']) && is_numeric($filterArray['pl'])) {
            $plFilms = FilmOwners::where('owner_id', CHhelper::filterInputInt($filterArray['pl']))->lists('films_id')->toArray();
            $filterPl .= " AND cc_films.id IN  ('".implode("','",$plFilms)."') ";
        }
        if (!empty($filterArray['cp']) && is_numeric($filterArray['cp'])) {
            $cpFilms = FilmOwners::where('owner_id', CHhelper::filterInputInt($filterArray['cp']))->lists('films_id')->toArray();
            $filterCp .= " AND cc_films.id IN  ('".implode("','",$cpFilms)."') ";
        }
        if (!empty($plFilms) && !empty($cpFilms)){
            $filterFilms =  array_intersect($plFilms,$cpFilms);
            $filter .=  " AND cc_films.id IN  ('".implode("','",$filterFilms)."') ";
        }
        else
            $filter .=  $filterPl.$filterCp;

        /**/
        if ($filterArray['vaultStatus'] == 1) {
            $preQuery  = "INNER JOIN cc_channels_contracts ON cc_channels_contracts.bcontracts_id=cc_base_contracts.id AND cc_channels_contracts.channel_id=".$this->storeID."
                WHERE 1 = 1 $filterCp $filter  AND cc_films.deleted=0 ";
        }
        else if ($filterArray['vaultStatus']==2) {
            $preQuery = "LEFT OUTER JOIN cc_channels_contracts ON cc_channels_contracts.bcontracts_id=cc_base_contracts.id AND cc_channels_contracts.channel_id=".$this->storeID."
                WHERE 1=1  $filter AND cc_channels_contracts.id IS NULL   AND cc_films.deleted=0";
        }
        else  {
            $preQuery = "LEFT OUTER JOIN cc_channels_contracts ON cc_channels_contracts.bcontracts_id=cc_base_contracts.id AND cc_channels_contracts.channel_id=".$this->storeID."
                WHERE 1=1 $filter  AND cc_films.deleted=0";
        }

        $q="Select cc_films.*,cc_films.id as filmID,cc_vaults.*,cc_vaults.id as vaultID, cc_channels_contracts.id as channelContractID  FROM cc_vaults
                    INNER JOIN cc_films ON cc_films.id=cc_vaults.films_id
                    INNER JOIN cc_base_contracts ON cc_base_contracts.films_id=cc_films.id
                    $preQuery GROUP BY cc_vaults.films_id ORDER BY $sort ";

        $res = Film::hydrateRaw($q)->keyBy('id');
        //dd($res);
        $qt="Select COUNT(cc_films.id) as count  FROM cc_vaults
                    INNER JOIN cc_films ON cc_films.id=cc_vaults.films_id
                    INNER JOIN cc_base_contracts ON cc_base_contracts.films_id=cc_films.id
                    $preQuery ";

        $resTotal = Film::hydrateRaw($qt);

        $resTotal = $resTotal->first()->total;
       // $resTotal= G('DB')->query($qt);
        //dd($res);



       // $res_deleted = G('DB')->query("SELECT cc_vaults.films_id, cc_vaults.companies_id, cc_vaults.delete_dt  FROM cron_jobs inner join cc_vaults on cron_jobs.films_id=cc_vaults.films_id");
        $resDeleted = CronJobs::join('cc_vaults', 'cron_jobs.films_id', '=', 'cc_vaults.films_id')->select('cc_vaults.films_id', 'cc_vaults.companies_id', 'cc_vaults.delete_dt')->get();

        foreach($resDeleted as $key => $val) {
            if($val->delete_dt > 0)
                $filmsDelete['AllTeritories'][$val->films_id] = $val->delete_dt;
            else
                $filmsDelete['InSomeTeritories'][$val->films_id] = 1;
        }

        $films = $this->getVaultAllFilms();

        $filmStores = $this->getFilmsStores($films);
        $filmsOwners = $this->getOwnerFilms();
        $filmsContentProviders = $this->getFilmsContentProviders($films);

        $items = new LengthAwarePaginator($res, $resTotal, $this->limit, $this->page);

        return compact('items', 'filmsOwners', 'filmStores', 'filmsContentProviders');
        /*
        $this->classObj->filmsDelete =$filmsDelete;*/
    }

    private function getFilmsStores($films)
    {
        return Store::join('fk_films_owners', 'cc_channels.id', '=', 'fk_films_owners.owner_id')
                ->where('type', '1')
                ->whereIn('fk_films_owners.films_id', $films)
                ->select('cc_channels.title', 'fk_films_owners.films_id')
                ->get()->keyBy('films_id')->lists('title', 'films_id');
    }

    public function getFilmsContentProviders($films)
    {
        return Company::join('fk_films_owners', 'cc_companies.id', '=', 'fk_films_owners.owner_id')
            ->where('type', '0')
            ->whereIn('fk_films_owners.films_id', $films)
            ->select('cc_companies.title', 'fk_films_owners.films_id')
            ->get()->keyBy('films_id')->lists('title', 'films_id');
    }

    /**
     *@POST("/xchange/soloActAddToStore")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function soloActAddToStore()
    {
        DB::transaction(function () {
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
        });
    }    
	
	/**
     *@POST("/xchange/soloActDeleteFromStore")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function soloActDeleteFromStore()
    {
        DB::transaction(function () {
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
        });
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
                    DB::transaction(function () {
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
                    });
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
                    DB::transaction(function () {
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
                    });
				}			
			}		
		}
    }	

}

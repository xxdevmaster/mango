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
use App\Models\ChannelsVaults;
use App\Models\Sliders;

class XchangeTitlesController extends Controller
{
    private $request;

    private $authUser;

    private $storeID;

    private $companyID;

    private $limit = 20;

    private $offset = 0;

    private $page = 0;

    private $orderBy = 'id';

    private $orderType = 'asc';

    private $filterContentProviderID = false;

    private $searchWord = false;

    private $stores = [];

    private $deletedFilmsForXchange = [];

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
        $data = $this->getFilms();

        return view('xchange.xchangeTitles.xchangeTitles', compact('companies'), $data);
    }

    /**
     * Get content providers.
     * @return collection
     */
    public function getContentProviders()
    {
        return Company::join('cc_vaults', 'cc_companies.id', '=', 'cc_vaults.companies_id')
            ->where('cc_companies.title', '<>', '')
            ->groupBy('cc_companies.id')
            ->select('cc_companies.id', 'cc_companies.title')
            ->lists('title', 'id');
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
                    ->get()->lists('id', 'id');
    }

    /**
     * Get all films, pagintion.
     * @return array
     */
    public function getFilms()
    {
        $films = Vaults::join('cc_films', 'cc_films.id', '=', 'cc_vaults.films_id')
                        ->join('cc_base_contracts', 'cc_base_contracts.films_id', '=', 'cc_films.id')
                        ->where('cc_films.deleted', 0);

        $filter = (!empty($this->request->input('filter')) && is_array($this->request->input('filter'))) ? $this->request->input('filter') : false;
        if($filter)
        {
            $this->searchWord = !empty($filter['searchWord']) ? CHhelper::filterInput($filter['searchWord']) : false;
            $this->filterContentProviderID = (!empty($filter['pl']) && is_numeric($filter['pl'])) ? CHhelper::filterInputInt($filter['pl']) : false;

            if(!empty($filter['order']) && ($filter['order'] == 'id' || $filter['order'] == 'title'))
                $this->orderBy = CHhelper::filterInput($filter['order']);
            if(!empty($filter['orderType']) && ($filter['orderType'] == 'asc' || $filter['orderType'] == 'desc'))
                $this->orderType = CHhelper::filterInput($filter['orderType']);
        }

        if(!empty($filter['vaultStatus']))
        {
            if($filter['vaultStatus'] == 1)
            {
                $films->join('cc_channels_contracts', function ($join) {
                    $join->on('cc_channels_contracts.bcontracts_id', '=', 'cc_base_contracts.id')
                        ->where('cc_channels_contracts.channel_id','=', $this->storeID);
                });
            }else {
                $films->leftJoin('cc_channels_contracts', function ($join) {
                    $join->on('cc_channels_contracts.bcontracts_id', '=', 'cc_base_contracts.id')
                        ->where('cc_channels_contracts.channel_id','=', $this->storeID);
                })->whereNull('cc_channels_contracts.id');
            }
        }else {
            $films->leftJoin('cc_channels_contracts', function ($join) {
                $join->on('cc_channels_contracts.bcontracts_id', '=', 'cc_base_contracts.id')
                    ->where('cc_channels_contracts.channel_id','=', $this->storeID);
            });
        }

        if($this->searchWord)
        {
            $films->where(function($query){
                $query->where('cc_films.title', 'like', "$this->searchWord%")
                    ->orWhere('cc_films.id', 'like', "$this->searchWord%");
            });
        }

        if($this->filterContentProviderID)
        {
            $contentProviderFilmsIDS = FilmOwners::where('owner_id', $this->filterContentProviderID)->where('type', 0)->lists('films_id');
            $films = $films->whereIn('cc_films.id', $contentProviderFilmsIDS);
        }

        $filmsTotal = DB::table(DB::raw("({$films->groupBy('cc_vaults.films_id')->select('cc_films.id as id', 'cc_vaults.id as vaultID', 'cc_channels_contracts.id as channelContractID')->toSql()}) as tmpTable"))->mergeBindings($films->getQuery())->count();
        $films = $films->select('cc_films.*', 'cc_vaults.*', 'cc_vaults.id as vaultID', 'cc_channels_contracts.id as channelContractID')
                       ->groupBy('cc_vaults.films_id')->orderBY('cc_films.'.$this->orderBy, $this->orderType)->limit($this->limit)->skip($this->offset)->get()->keyBy('films_id');

        $films = new LengthAwarePaginator($films, $filmsTotal, $this->limit, $this->page);
        $filmStores = $this->getFilmsStores($this->getVaultAllFilms());

        $orderBy = $this->orderBy;
        $orderType = $this->orderType;

        $cronJobsFilms= CronJobs::join('cc_vaults', 'cron_jobs.films_id', '=', 'cc_vaults.films_id')->select('cc_vaults.films_id', 'cc_vaults.companies_id', 'cc_vaults.delete_dt')->get();

        if(!$cronJobsFilms->isEmpty()) {
            $cronJobsFilms->each(function($film) {
                if($film->delete_dt > 0)
                    $this->deletedFilmsForXchange['AllTeritories'][$film->films_id] = $film->delete_dt;
                else
                    $this->deletedFilmsForXchange['InSomeTeritories'][$film->films_id] = 1;
            });

        }
        $deletedFilmsForXchange = $this->deletedFilmsForXchange;
        $ownerFilmIDS = $this->getOwnerFilms();

        return compact('films', 'filmStores', 'orderBy', 'orderType', 'deletedFilmsForXchange', 'ownerFilmIDS');
    }

    /**
     *@POST("/xchange/titlesFilter")
     * @Middleware("auth")
     */
    public function titlesFilter()
    {
        $data = $this->getFilms();
        return view('xchange.xchangeTitles.list', $data)->render();
    }

    /**
     *@POST("/xchange/pager")
     * @Middleware("auth")
     */
    public function pager()
    {
        $this->page = (!empty($this->request->Input('page')) && is_numeric($this->request->Input('page'))) ? CHhelper::filterInputInt($this->request->Input('page')) : 0;

        if(($this->page - 1) != 0)
            $this->offset  = ($this->page - 1)*20;

        $data = $this->getFilms();
        return view('xchange.xchangeTitles.list', $data)->render();
    }

    private function getFilmsStores($films)
    {
        $filmStores = Store::join('fk_films_owners', 'cc_channels.id', '=', 'fk_films_owners.owner_id')
                ->where('type', '1')
                ->whereIn('fk_films_owners.films_id', $films)
                ->select('cc_channels.title', 'fk_films_owners.films_id')
                ->get()->keyBy('films_id')->lists('title', 'films_id');

        foreach($filmStores as $storeID => $storeTitle)
            $this->stores[$storeID][] = $storeTitle;

        return $this->stores;
    }

    /**
     * Add the film to account store.
     * @POST("/xchange/soloActAddToStore")
     * @Middleware("auth")
     */
    public function soloActAddToStore()
    {
        $this->filmID = (!empty($this->request->Input('filmID')) && is_numeric($this->request->Input('filmID'))) ? CHhelper::filterInputInt($this->request->Input('filmID')) : false;

        if(!$this->filmID)
            return [
                'error' => '1',
                'message' => 'Invalid argument film id',
            ];

        DB::transaction(function () {
            $film = Film::find($this->filmID);
            $baseContracts = $film->baseContract;

            ChannelsFilms::create([
                'channels_id' => $this->storeID ,
                'films_id' => $film->id
            ]);

            FilmOwners::create([
                'owner_id' => $this->storeID ,
                'films_id' => $film->id ,
                'type' => '1',
                'role' => '4'
            ]);

            ChannelsContracts::create([
                'channel_id' => $this->storeID ,
                'bcontracts_id' => $baseContracts->id ,
                'film_status' => '1'
            ]);

            $vaults = Vaults::where('films_id', $film->id)->lists('id');

            foreach($vaults as $vaultID){
                ChannelsVaults::create([
                    'vaults_id' => $vaultID,
                    'channels_id' => $this->storeID
                ]);
            }
        });

        $data = $this->getFilms();
        return view('xchange.xchangeTitles.list', $data)->render();
    }    
	
	/**
     * Delete the film from account store.
     * @POST("/xchange/soloActDeleteFromStore")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function soloActDeleteFromStore()
    {
        DB::transaction(function () {
            $film = $this->request->film->first();
            $baseContract = $film->baseContract;

            ChannelsFilms::where('channels_id', $this->storeID)->where('films_id', $film->id)->delete();
            FilmOwners::where('owner_id', $this->storeID)->where('films_id', $film->id)->where('type', '1')->delete();
            ChannelsContracts::where('channel_id', $this->storeID)->where('bcontracts_id', $baseContract->id)->delete();

            $sildersID = Sliders::where('channel_id', $this->storeID)->select('id')->get()->keyBy('id')->toArray();
            FilmSlidersImages::where('films_id', $film->filmId)
                ->whereIn('sliders_id', $sildersID)
                ->update([
                    'films_id' => ''
                ]);

            $collectionsID = Collections::where('channels_id', $this->storeID)->select('id')->get()->keyBy('id')->toArray();
            CollectionsFilms::where('films_id', $film->id)
                ->whereIn('collections_id', $collectionsID)
                ->delete();

            $subchannelsID = Subchannels::where('channels_id', $this->storeID)->select('id')->get()->keyBy('id')->toArray();
            SubchannelsFilms::where('films_id', $film->id)
                ->whereIn('subchannels_id', $subchannelsID);

            $vaults = Vaults::where('films_id', $film->id)->get()->keyBy('id');

            foreach($vaults as $key => $val){
                ChannelsVaults::where('channels_id', $this->storeID)->where('vaults_id', $key)->delete();
            }
        });

        $data = $this->getFilms();
        return view('xchange.xchangeTitles.list', $data)->render();
    }
	
	/**
     * Add the films from xchange to store.
     * @POST("/xchange/bulkActAddToStore")
     * @Middleware("auth")
     */	
    public function bulkActAddToStore()
	{
		if(!empty($this->request->Input('filmsNotInMyStore'))){
			foreach($this->request->Input('filmsNotInMyStore') as $filmID => $status){
				if ($status == 'on'){
                    $this->filmID = CHhelper::filterInputInt($filmID);
                    DB::transaction(function () {
                        $baseContracts = Film::where('id', $this->filmID)->first()->baseContract;

                        ChannelsFilms::create([
                            'channels_id' => $this->storeID ,
                            'films_id' => $this->filmID
                        ]);

                        FilmOwners::create([
                            'owner_id' => $this->storeID ,
                            'films_id' => $this->filmID ,
                            'type' => '1',
                            'role' => '4'
                        ]);

                        ChannelsContracts::create([
                            'channel_id' => $this->storeID ,
                            'bcontracts_id' => $baseContracts->id ,
                            'film_status' => '1'
                        ]);

                        $vaults = Vaults::where('films_id', $this->filmID)->lists('id');

                        foreach($vaults as $vaultID){
                            ChannelsVaults::create([
                                'vaults_id' => $vaultID ,
                                'channels_id' => $this->storeID
                            ]);
                        }
                    });
				}			
			}		
		}

        $data = $this->getFilms();
        return view('xchange.xchangeTitles.list', $data)->render();
    }

	/**
     * Delete the films from store.
     * @POST("/xchange/bulkActDeleteFromStore")
     * @Middleware("auth")
     */		
    public function bulkActDeleteFromStore()
	{
		if(!empty($this->request->Input('filmsInMyStore'))){
			foreach($this->request->Input('filmsInMyStore') as $filmID => $status){
                if ($status == 'on'){

                    $this->filmID = CHhelper::filterInputInt($filmID);
                    DB::transaction(function () {
                        $baseContracts = Film::where('id', $this->filmID)->first()->baseContract;

                        ChannelsFilms::where('channels_id', $this->storeID)->where('films_id', $this->filmID)->delete();
                        FilmOwners::where('owner_id', $this->storeID)->where('films_id', $this->filmID)->where('type', '1')->delete();
                        ChannelsContracts::where('channel_id', $this->storeID)->where('bcontracts_id', $baseContracts->id)->delete();

                        $sildersID = Sliders::where('channel_id', $this->storeID)->select('id')->get()->keyBy('id')->toArray();
                        FilmSlidersImages::where('films_id', $this->filmID)
                            ->whereIn('sliders_id', $sildersID)
                            ->update([
                                'films_id' => ''
                            ]);

                        $collectionsID = Collections::where('channels_id', $this->storeID)->select('id')->get()->keyBy('id')->toArray();
                        CollectionsFilms::where('films_id', $this->filmID)
                            ->whereIn('collections_id', $collectionsID)
                            ->delete();

                        $subchannelsID = Subchannels::where('channels_id', $this->storeID)->select('id')->get()->keyBy('id')->toArray();
                        SubchannelsFilms::where('films_id', $this->filmID)
                            ->whereIn('subchannels_id', $subchannelsID);

                        $vaults = Vaults::where('films_id', $this->filmID)->get()->keyBy('id');

                        foreach($vaults as $key => $val){
                            ChannelsVaults::where('channels_id', $this->storeID)->where('vaults_id', $key)->delete();
                        }
                    });
				}			
			}		
		}

        $data = $this->getFilms();
        return view('xchange.xchangeTitles.list', $data)->render();
    }

    private function isFilmNotInStore($filmID)
    {
        $film = Film::join('cc_base_contracts', 'cc_base_contracts.films_id', '=', 'cc_films.id')
                        ->join('cc_channels_contracts', 'cc_channels_contracts.bcontracts_id', '=', 'cc_base_contracts.id')
                        ->where('cc_channels_contracts.channel_id', $this->storeID)
                        ->where('cc_films.deleted', 0)
                        ->where('cc_films.id', $filmID)->get();

      return $film->isEmpty();
    }

    public function getAvailableCountries($filmID)
    {
        $out = [];
        $availableCountries = Vaults::join('cc_companies', 'cc_companies.id', '=', 'cc_vaults.companies_id')
                    ->join('cc_geo_contracts', function ($join) {
                        $join->on('cc_geo_contracts.companies_id', '=', 'cc_vaults.companies_id')
                             ->where('cc_geo_contracts.films_id', '=', 'cc_vaults.films_id');
                    })->join('cc_countries', 'cc_countries.id', '=', 'cc_geo_contracts.countries_id')
                    ->where('cc_vaults.films_id', $filmID)
                    ->where('cc_geo_contracts.deleted', 0)
                    ->select('cc_vaults.*', 'cc_companies.title', 'cc_geo_contracts.countries_id' ,'cc_countries.title AS countryTitle')
                    ->get();

        if(!$availableCountries->isEmpty())
            foreach($availableCountries as $availableCountry) {
                $out[$availableCountry['companies_id']]['info'] = $availableCountry['title'];
                $out[$availableCountry['companies_id']]['delete_dt'] = $availableCountry['delete_dt'];
                $out[$availableCountry['companies_id']]['countries'][] = $availableCountry['country_title'];
            }

        return $out;
    }

    /**
     * Draw film available countries.
     * @POST("/xchange/drawAvailableCountries")
     * @Middleware("auth")
     * @return laravel response view('xchange.store.countries')
    */
    public function drawAvailableCountries()
    {
        $this->filmID = (!empty($this->request->Input('filmID')) && is_numeric($this->request->Input('filmID'))) ? CHhelper::filterInputInt($this->request->Input('filmID')) : false;

        if(!$this->filmID)
            return [
                'error' => '1',
                'message' => 'Invalid argument film id',
            ];

        $isFilmNotInStore = $this->isFilmNotInStore($this->filmID);
        $availableCountries = $this->getAvailableCountries($this->filmID);

        $channelCompanies = ChannelsVaults::join('cc_vaults', 'cc_vaults.id', '=', 'fk_channels_vaults.vaults_id')
                            ->where('cc_vaults.films_id', $this->filmID)
                            ->where('fk_channels_vaults.channels_id', $this->storeID)
                            ->lists('companies_id', 'companies_id');

        $filmID = $this->filmID;

        return view('xchange.xchangeTitles.countries', compact('availableCountries', 'isFilmNotInStore', 'channelCompanies', 'filmID'))->render();
    }

    /**
     *
     * @POST("/xchange/connectCP2PL")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function connectCP2PL()
    {
        $this->filmID = $this->request->filmID;
        $companyID = (!empty($this->request->Input('companyID')) && is_numeric($this->request->Input('companyID'))) ? CHhelper::filterInputInt($this->request->Input('companyID')) : false;

        if(!$companyID)
            return [
                'error' => '1',
                'message' => 'Invalid argument company id',
            ];

        $vaults = Vaults::where('cc_vaults.films_id', $this->filmID)->where('cc_vaults.companies_id', $companyID)->lists('id');
        foreach($vaults as $vaultID) {
            ChannelsVaults::create([
                'vaults_id' => $vaultID ,
                'channels_id' => $this->StoreID
            ]);
        }

        return $this->drawAvailableCountries();
    }

    /**
     *
     * @POST("/xchange/disconnectCP2PL")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function disconnectCP2PL()
    {
        $this->filmID = $this->request->filmID;
        $companyID = (!empty($this->request->Input('companyID')) && is_numeric($this->request->Input('companyID'))) ? CHhelper::filterInputInt($this->request->Input('companyID')) : false;

        if(!$companyID)
            return [
                'error' => '1',
                'message' => 'Invalid argument company id',
            ];

        $vaults = Vaults::where('cc_vaults.films_id', $this->filmID)->where('cc_vaults.companies_id', $companyID)->lists('id');

        foreach($vaults as $vaultID) {
            ChannelsVaults::where('channels_id', $this->StoreID)->where('vaults_id', $vaultID)->delete();
        }

        return $this->drawAvailableCountries();
    }

}

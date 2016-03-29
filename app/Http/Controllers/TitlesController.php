<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Http\Requests;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Film;
use App\FilmOwners;
use App\Store;
use App\Company;
use Illuminate\Http\Request;
use App\Libraries\CHhelper\CHhelper;

class TitlesController extends Controller
{
    private $request;

    private $authUser;

    private $accountID;

    private $storeID;

    private $companyID;

    private $limit = 20;

    private $offset = 0;

    private $page = 0;

    private $orderBy = 'id';

    private $orderType = 'asc';

    private $searchWord = '';

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->authUser = Auth::user();
        $this->accountID = $this->authUser->account->id;
        $this->storeID = $this->authUser->account->platforms_id;
        $this->companyID = $this->authUser->account->companies_id;
    }

    public function titlesShow()
    {
        $films = $this->getData();
        $stores = $this->getStores($films['allFilmsIDS']);
        $companies = $this->getContentProviders($films['allFilmsIDS']);

        return view('titles.index', compact('companies', 'stores'), $films);
    }

    /**
     * Get all films, pagintion, films stores and films content providers.
     * @return array
    */	
    private function getData()
    {
        $filter = (!empty($this->request->input('filter')) && is_array($this->request->input('filter'))) ? $this->request->input('filter') : false;

        if($filter)
        {
			$this->searchWord = !empty($filter['searchWord']) ? CHhelper::filterInput($filter['searchWord']) : false;
            $filterStoreID = (!empty($filter['pl']) && is_numeric($filter['pl'])) ? CHhelper::filterInputInt($filter['pl']) : false;
            $filterContentProviderID = (!empty($filter['cp']) && is_numeric($filter['cp'])) ? CHhelper::filterInputInt($filter['cp']) : false;
            if ($filterStoreID) {
                $storeFilmsIDS = FilmOwners::where('owner_id', $filterStoreID)->where('type', 1)->lists('films_id')->toArray();
            }
            if ($filterContentProviderID) {
                $contentProviderFilmsIDS = FilmOwners::where('owner_id', $filterContentProviderID)->where('type', 0)->lists('films_id')->toArray();
            }

            if(!empty($storeFilmsIDS) && !empty($contentProviderFilmsIDS))
                $filterIDS = array_intersect($storeFilmsIDS, $contentProviderFilmsIDS);
            elseif(!empty($storeFilmsIDS))
                $filterIDS = $storeFilmsIDS;
            elseif(!empty($contentProviderFilmsIDS))
                $filterIDS = $contentProviderFilmsIDS;

            if(!empty($filter['order']) && ($filter['order'] == 'id' || $filter['order'] == 'title'))
                $this->orderBy = CHhelper::filterInput($filter['order']);
            if(!empty($filter['orderType']) && ($filter['orderType'] == 'asc' || $filter['orderType'] == 'desc'))
                $this->orderType = CHhelper::filterInput($filter['orderType']);
        }

        if( $this->storeID > 0 && $this->companyID > 0)
        {
            $union = Film::distinct()->join('fk_films_owners', 'fk_films_owners.films_id', '=', 'cc_films.id')
                                    ->where('fk_films_owners.owner_id', $this->companyID)
                                    ->where('fk_films_owners.type', 0)
                                    ->where('cc_films.deleted', 0);

            $films = Film::distinct()->join('cc_base_contracts', 'cc_base_contracts.films_id', '=', 'cc_films.id')
                                    ->join('cc_channels_contracts', 'cc_channels_contracts.bcontracts_id', '=', 'cc_base_contracts.id')
                                    ->where('cc_channels_contracts.channel_id', $this->storeID)
                                    ->where('cc_films.deleted', 0);

            $allFilmsIDS = $films->union($union->select('cc_films.id'))->select('cc_films.id')->lists('id');

            $films->getQuery()->unions = null; /* Clear All Unions */
            $films->setBindings([], 'union'); /* Clear All Union Bindings */

            if(isset($this->searchWord))
            {
                $union->where(function($query){
                    $query->where('cc_films.title', 'like', "$this->searchWord%")
                        ->orWhere('cc_films.id', 'like', "$this->searchWord%");
                });

                $films->where(function($query){
                    $query->where('cc_films.title', 'like', "$this->searchWord%")
                        ->orWhere('cc_films.id', 'like', "$this->searchWord%");
                });
            }

            if(isset($filterIDS))
            {
                $union = $union->whereIn('cc_films.id', $filterIDS);
                $films = $films->whereIn('cc_films.id', $filterIDS);
            }

            $total = $films->select('cc_films.id as id');
            $total = $total->union($union->select('cc_films.id as id'));
            $total = DB::table(DB::raw("({$total->toSql()}) as total"))->mergeBindings($total->getQuery())->count();

            $films->getQuery()->unions = null; /* Clear All Unions */
            $films->setBindings([], 'union'); /* Clear All Union Bindings */

            $films = $films->union($union->select('cc_films.*'))->select('cc_films.*')->orderBY($this->orderBy, $this->orderType)->skip($this->offset)->take($this->limit)->get()->keyBy('id');
        }
        elseif( $this->storeID > 0)
        {
            $films = Film::join('cc_base_contracts', 'cc_base_contracts.films_id', '=', 'cc_films.id')
                              ->join('cc_channels_contracts', 'cc_channels_contracts.bcontracts_id', '=', 'cc_base_contracts.id')
                              ->where('cc_channels_contracts.channel_id', $this->storeID)
                              ->where('cc_films.deleted', 0);

            $allFilmsIDS = $films->select('cc_films.id')->lists('id');

            if(isset($this->searchWord))
            {
                $films->where(function($query){
                    $query->where('cc_films.title', 'like', "$this->searchWord%")
                          ->orWhere('cc_films.id', 'like', "$this->searchWord%");
                });
            }

            if(!empty($filterIDS))
                $films = $films->whereIn('cc_films.id', $filterIDS);

            $total = $films->count();
            $films = $films->select('cc_films.*')->orderBY($this->orderBy, $this->orderType)->skip($this->offset)->take($this->limit)->get()->keyBy('id');


        }
        elseif( $this->companyID > 0)
        {
            $films = Film::join('fk_films_owners', 'fk_films_owners.films_id', '=', 'cc_films.id')
                            ->where('fk_films_owners.owner_id', $this->companyID)
                            ->where('fk_films_owners.type', 0)
                            ->where('cc_films.deleted', 0);

            $allFilmsIDS = $films->select('cc_films.id')->lists('id');

            if(isset($this->searchWord))
            {
                $films->where(function($query){
                    $query->where('cc_films.title', 'like', "$this->searchWord%")
                          ->orWhere('cc_films.id', 'like', "$this->searchWord%");
                });
            }

            if(isset($filterIDS))
                $films = $films->whereIn('cc_films.id', $filterIDS);


            $total = $films->count();
            $films = $films->select('cc_films.*')->orderBY($this->orderBy, $this->orderType)->skip($this->offset)->take($this->limit)->get()->keyBy('id');
        }

        $filmsIDS = $films->lists('id');
        $filmCP = $this->getFilmsContentProviders($filmsIDS);
        $filmStores = $this->getFilmsStores($filmsIDS);

        $items = new LengthAwarePaginator($films, $total, $this->limit, $this->page);

        $orderBy = $this->orderBy;
        $orderType = $this->orderType;
        return compact('allFilmsIDS', 'items', 'orderBy', 'orderType', 'filmCP', 'filmStores');
    }
	
    /**
     * Get all content providers.
     * @param  array or collection  $filmsIDS
     * @return collection
    */		
    private function getContentProviders($filmsIDS)
    {
        return Company::join('fk_films_owners', 'cc_companies.id', '=', 'fk_films_owners.owner_id')
                        ->whereIn('fk_films_owners.films_id', $filmsIDS)
                        ->where('fk_films_owners.type', 0)
                        ->where('cc_companies.title', '<>', '')
                        ->select('cc_companies.title', 'cc_companies.id')
                        ->groupBy('cc_companies.id')->lists('title', 'id');
    }
	
    /**
     * Get all stores.
     * @param  array or collection  $filmsIDS
     * @return collection
     */	
    private function getStores($filmsIDS)
    {
        return Store::join('fk_films_owners', 'cc_channels.id', '=', 'fk_films_owners.owner_id')
                        ->whereIn('fk_films_owners.films_id', $filmsIDS)
                        ->where('fk_films_owners.type', 1)
                        ->where('cc_channels.title', '<>', '')
                        ->select('cc_channels.title', 'cc_channels.id')
                        ->groupBy('cc_channels.id')->lists('title', 'id');
    }

    /**
     * Get a film content providers.
     * @param  array or collection  $filmsIDS
     * @return array
    */
    private function getFilmsContentProviders($filmsIDS)
    {
        $contentProviders = array();
        $companies = Company::distinct()->select('cc_companies.title', 'fk_films_owners.films_id')
                        ->join('fk_films_owners', 'cc_companies.id', '=', 'fk_films_owners.owner_id')
                        ->whereIn('fk_films_owners.films_id', $filmsIDS)
                        ->where('fk_films_owners.type', 0)
                        ->get();

        foreach($companies as $company)
            $contentProviders[$company->films_id][] = $company->title;

        return $contentProviders;
    }

    /**
     * Get a film stores.
     * @param  array or collection  $filmsIDS
     * @return array
    */
    private function getFilmsStores($filmsIDS)
    {
        $platforms = array();
        $stores = Store::distinct()->select('cc_channels.title', 'fk_films_owners.films_id')
                        ->join('fk_films_owners', 'cc_channels.id', '=', 'fk_films_owners.owner_id')
                        ->whereIn('fk_films_owners.films_id', $filmsIDS)
                        ->where('fk_films_owners.type', 1)
                        ->get();

        foreach($stores as  $store)
            $platforms[$store->films_id][] = $store->title;

        return $platforms;
    }


    /**
     *@POST("/titles/titlesFilter")
     * @Middleware("auth")
     */
    public function titlesFilter()
    {
        $films = $this->getData();
        return view('titles.partials.list', $films)->render();
    }

    /**
     *@POST("/titles/pager")
     * @Middleware("auth")
     */
    public function pager()
    {
        $this->page = (!empty($this->request->Input('page')) && is_numeric($this->request->Input('page'))) ? CHhelper::filterInputInt($this->request->Input('page')) : 0;
		
		if(($this->page - 1) != 0)
			$this->offset  = ($this->page - 1)*20;

		$films = $this->getData();
		return view('titles.partials.list', $films)->render();
    }
}

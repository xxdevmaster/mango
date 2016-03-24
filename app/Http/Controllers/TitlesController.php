<?php

namespace App\Http\Controllers;

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
        $films = $this->getFilms();
        $filmsIDS = Film::getAccountAllTitles($this->storeID, $this->companyID, ['cc_films.id'], '')->lists('id');
        $stores = $this->getStores($filmsIDS);
        $companies = $this->getContentProviders($filmsIDS);

        return view('titles.index', compact('companies', 'stores'), $films);
    }


    private function getFilms()
    {
        $filter = (!empty($this->request->input('filter')) && is_array($this->request->input('filter'))) ? $this->request->input('filter') : false;
        $orderBy = 'id';
        $orderType = 'ASC';
        $where = '';

        if($filter)
        {
            if(!empty($filter['searchWord']))
            {
                if(is_numeric($filter['searchWord']))
                {
                    $searchword = CHhelper::filterInputInt($filter['searchWord']);
                    $where = ['and cc_films.id like', "'$searchword%'"];
                } else
                {
                    $searchword = CHhelper::filterInput($filter['searchWord']);
                    $where = ['and cc_films.title like', "'$searchword%'"];
                }
            }

            $filterStoreID = (!empty($filter['pl']) && is_numeric($filter['pl'])) ? CHhelper::filterInputInt($filter['pl']) : false;
            $filterContentProviderID = (!empty($filter['cp']) && is_numeric($filter['cp'])) ? CHhelper::filterInputInt($filter['cp']) : false;
            if ($filterStoreID) {
                $storeFilmsIDS = FilmOwners::where('owner_id', $filterStoreID)->where('type', 1)->lists('films_id', 'films_id')->toArray();
            }
            if ($filterContentProviderID) {
                $contentProviderFilmsIDS = FilmOwners::where('owner_id', $filterContentProviderID)->where('type', 0)->lists('films_id', 'films_id')->toArray();
            }

            if(!empty($storeFilmsIDS) && !empty($contentProviderFilmsIDS))
                $where[] = " AND cc_films.id IN  ('".implode("','", array_intersect($storeFilmsIDS, $contentProviderFilmsIDS))."') ";
            elseif(!empty($storeFilmsIDS))
                $where[] = " AND cc_films.id IN  ('".implode("','", $storeFilmsIDS)."') ";
            elseif(!empty($contentProviderFilmsIDS))
                $where[] = " AND cc_films.id IN  ('".implode("','", $contentProviderFilmsIDS)."') ";

            if(!empty($filter['order']) && ($filter['order'] == 'id' || $filter['order'] == 'title'))
                $orderBy = CHhelper::filterInput($filter['order']);

            if(!empty($filter['orderType']) && ($filter['orderType'] == 'ASC' || $filter['orderType'] == 'DESC'))
                $orderType = CHhelper::filterInput($filter['orderType']);
        }

        $total = Film::getAccountAllTitlesCount($this->storeID, $this->companyID, $where)[0]->total;
        $films = Film::getAccountAllTitles($this->storeID, $this->companyID, ['cc_films.*'], $where, $orderBy, $orderType, $this->limit, $this->offset)->keyBy('id');

        $filmCP = $this->getFilmsContentProviders($films->lists('id'));
        $filmStores = $this->getFilmsStores($films->lists('id'));

        $items = new LengthAwarePaginator($films, $total, $this->limit, $this->page);

        return compact('items', 'orderBy', 'orderType', 'filmCP', 'filmStores');
    }

    private function getContentProviders($filmsIDS)
    {
        return Company::join('fk_films_owners', 'cc_companies.id', '=', 'fk_films_owners.owner_id')
                        ->whereIn('fk_films_owners.films_id', $filmsIDS)
                        ->where('fk_films_owners.type', 0)
                        ->where('cc_companies.title', '<>', '')
                        ->select('cc_companies.title', 'cc_companies.id')
                        ->groupBy('cc_companies.id')->lists('title', 'id');
    }
    private function getStores($filmsIDS)
    {
        return Store::join('fk_films_owners', 'cc_channels.id', '=', 'fk_films_owners.owner_id')
                        ->whereIn('fk_films_owners.films_id', $filmsIDS)
                        ->where('fk_films_owners.type', 1)
                        ->where('cc_channels.title', '<>', '')
                        ->select('cc_channels.title', 'cc_channels.id')
                        ->groupBy('cc_channels.id')->lists('title', 'id');
    }

    private function getFilmsContentProviders($filmsIDS)
    {
        $cps = array();
        $companies = Company::distinct()->select('cc_companies.title', 'fk_films_owners.films_id')
                        ->join('fk_films_owners', 'cc_companies.id', '=', 'fk_films_owners.owner_id')
                        ->whereIn('fk_films_owners.films_id', $filmsIDS)
                        ->where('fk_films_owners.type', 0)
                        ->get();

        foreach($companies as $company)
            $cps[$company->films_id][] = $company->title;

       return $cps;
    }

    private function getFilmsStores($filmsIDS)
    {
        $pls = array();
        $stores = Store::distinct()->select('cc_channels.title', 'fk_films_owners.films_id')
                        ->join('fk_films_owners', 'cc_channels.id', '=', 'fk_films_owners.owner_id')
                        ->whereIn('fk_films_owners.films_id', $filmsIDS)
                        ->where('fk_films_owners.type', 1)
                        ->get();

        foreach($stores as  $store)
            $pls[$store->films_id][] = $store->title;

        return $pls;
    }


    /**
     *@POST("/titles/titlesFilter")
     * @Middleware("auth")
     */
    public function titlesFilter()
    {
        $films = $this->getFilms();
        return view('titles.partials.list', $films)->render();
    }

    /**
     *@POST("/titles/pager")
     * @Middleware("auth")
     */
    public function pager()
    {
        $this->page = !empty($this->request->Input('page')) && is_numeric($this->request->Input('page')) ? CHhelper::filterInputInt($this->request->Input('page')) : 0;
        if($this->page){
            if(($this->page - 1) != 0)
                $this->offset  = ($this->page - 1)*20;

            $films = $this->getFilms();
            return view('titles.partials.list', $films)->render();
        }
    }
}

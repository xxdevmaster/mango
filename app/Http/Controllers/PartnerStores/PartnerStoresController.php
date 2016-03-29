<?php

namespace App\Http\Controllers\PartnerStores;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Libraries\CHhelper\CHhelper;
use Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use DB;
use App\Film;
use App\Store;

class PartnerStoresController extends Controller
{
    private $request;

    private $authUser;

    private $companyID;

    private $limit = 20;

    private $offset = 0;

    private $page = 0;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->authUser = Auth::user();
        $this->companyID = $this->authUser->account->company->id;
    }

    public function partnerStoresShow()
    {
        $stores = $this->getStores();
        $stores->each(function($store, $storeID){
            $store->setAttribute('titlesCount', $store->storeFilmsCount($this->getAllFilmsIDS()));
        });
        return view('partnerStores.partnerStores', compact('stores'));
    }

    /**
     * Get partner stores.
     * @return collection
     */
    private function getStores()
    {
        $searchWord = !empty($this->request->Input('searchWord')) ? CHhelper::filterInput($this->request->Input('searchWord')) : '';
        if($this->companyID == 1){
            $storesCount = Store::where('title', 'like', $searchWord.'%')->count();
            $stores = Store::where('title', 'like', $searchWord.'%')->orderBy('title')->limit($this->limit)->skip($this->offset)->get();
        }
        else{
            $storesCount = Store::join('fk_films_owners', 'cc_channels.id', '=', 'fk_films_owners.owner_id')
                                ->where('cc_channels.title','<>', '')
                                ->whereIN('fk_films_owners.films_id', $this->getAllFilmsIDS())
                                ->where('fk_films_owners.type', '1')
                                ->where('cc_channels.title', 'like', $searchWord.'%')
                                ->distinct()
                                ->count('cc_channels.id');

            $stores = Store::join('fk_films_owners', 'cc_channels.id', '=', 'fk_films_owners.owner_id')
                            ->where('cc_channels.title','<>', '')
                            ->whereIN('fk_films_owners.films_id', $this->getAllFilmsIDS())
                            ->where('fk_films_owners.type', '1')
                            ->where('cc_channels.title', 'like', $searchWord.'%')
                            ->select('cc_channels.id', 'cc_channels.title', 'cc_channels.logo')
                            ->groupBy('cc_channels.id')->limit($this->limit)->skip($this->offset)->get();
        }

        return new lengthawarepaginator($stores, $storesCount, $this->limit, $this->page);
    }

    /**
     *@POST("/partner/stores/filterSearch")
     * @Middleware("auth")
    */
    public function filterSearch()
    {
        $stores = $this->getStores();
        return view('partnerStores.storeList', compact('stores'))->render();
    }

    /**
     * Get company all films ids.
     * @return collection
    */
    public function getAllFilmsIDS()
    {
        if($this->companyID == 1) // cinehost
            return Film::where('deleted', '0')->lists('id');
        else
            return $this->authUser->account->company->films('cc_films.id')->lists('id');
    }

    /**
     *@POST("/partner/stores/pager")
     * @Middleware("auth")
     */
    public function storesPager()
    {
        $this->page = (!empty($this->request->Input('page')) && is_numeric($this->request->Input('page'))) ? CHhelper::filterInputInt($this->request->Input('page')) : false;
            if(($this->page - 1) != 0)
                $this->offset  = ($this->page - 1)*20;

        $stores = $this->getStores();
        return view('partnerStores.storeList', compact('stores'));
    }

    /**
     *@GET("/partner/stores/films/{storeID}")
     * @Middleware("auth")
     */
    public function partnerStoreFilms($storeID)
    {
        $partnerStoresFilms = $this->getStoreFilms($storeID);
        return view('partnerStores.storeFilms', $partnerStoresFilms);
    }

    /**
     *@POST("/partner/stores/films/pager")
     * @Middleware("auth")
    */
    public function storeFilmsPager()
    {
        $storeID = (!empty($this->request->Input('storeID')) && is_numeric($this->request->Input('storeID'))) ? CHhelper::filterInputInt($this->request->Input('storeID')) : false;
        $this->page = (!empty($this->request->Input('page')) && is_numeric($this->request->Input('page'))) ? CHhelper::filterInputInt($this->request->Input('page')) : 0;
        if($storeID){
            if(($this->page - 1) != 0)
                $this->offset  = ($this->page - 1)*20;

            $storesFilms = $this->getStoreFilms($storeID);
            return view('partnerStores.filmsList', $storesFilms);
        }
    }

    /**
     * Get get store films and pagination .
     * @param integer $storeID
     * @return array
     */
    private function getStoreFilms($storeID)
    {
        $store = Store::where('id', $storeID)->get()->first();
        $total = $store->storeFilmsCount($this->getAllFilmsIDS());
        $storesFilms = $store->storeFilms($this->getAllFilmsIDS(), $this->limit, $this->offset)->get()->keyBy('id');
        $paginator =  new lengthawarepaginator($storesFilms, $total, $this->limit, $this->page);
        return compact('store', 'paginator');
    }
}

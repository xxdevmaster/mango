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
        return view('partnerStores.partnerStores', compact('stores'));
    }

    private function getStores()
    {
        $condition = !empty($this->request->Input('searchWord')) ? CHhelper::filterInput($this->request->Input('searchWord')) : '';
        if($this->companyID == 1){
            $storesCount = Store::where('title', 'like', '%'.$condition.'%')->count();
            $stores = Store::where('title', 'like', '%'.$condition.'%')->orderBy('title')->limit($this->limit)->skip($this->offset)->get();
        }
        else{
            $storesCount = Store::join('fk_films_owners', 'cc_channels.id', '=', 'fk_films_owners.owner_id')
                ->where('cc_channels.title','<>', '')
                ->whereIN('fk_films_owners.films_id', $this->getAllFilms())
                ->where('fk_films_owners.type', '1')
                ->where('cc_channels.title', 'like', '%'.$condition.'%')
                ->select(DB::raw('COUNT(DISTINCT  cc_channels.id) as count'))
                ->get()
                ->first()->count;

            $stores = Store::join('fk_films_owners', 'cc_channels.id', '=', 'fk_films_owners.owner_id')
                ->where('cc_channels.title','<>', '')
                ->whereIN('fk_films_owners.films_id', $this->getAllFilms())
                ->where('fk_films_owners.type', '1')
                ->where('cc_channels.title', 'like', '%'.$condition.'%')
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
        return view('partnerStores.list_partial', compact('stores'))->render();
    }

    public function getAllFilms()
    {
        if($this->companyID == 1) // cinehost
            return Film::where('deleted', '0')->select('id')->get()->keyBy('id')->keys()->toArray();
        else
            return $this->authUser->account->company->films->keyBy('id')->keys()->toArray();
    }

    /**
     *@GET("/partner/stores/films/{storeID}")
     * @Middleware("auth")
     */
    public function partnerStoreFilms($storeID)
    {
        $partnerStoresFilms = $this->getPartnerStoresFilms($storeID);
        return view('partnerStores.storesFilms', $partnerStoresFilms);
    }

    /**
     *@POST("/partner/stores/films/pager")
     * @Middleware("auth")
     */
    public function partnerStoresFilmsPager()
    {
        $storeID = !empty($this->request->Input('storeID')) && is_numeric($this->request->Input('storeID')) ? CHhelper::filterInputInt($this->request->Input('storeID')) : false;
        $this->page = !empty($this->request->Input('page')) && is_numeric($this->request->Input('page')) ? CHhelper::filterInputInt($this->request->Input('page')) : false;
        if($storeID && $this->page){
            if(($this->page - 1) != 0)
                $this->offset  = ($this->page - 1)*20;

            $storesFilms = $this->getPartnerStoresFilms($storeID);
            return view('partnerStores.filmList_partial',  compact('current_menu'), $storesFilms);
        }
    }

    private function getPartnerStoresFilms($storeID)
    {
        $store = Store::where('id', $storeID)->get()->first();
        $total = $store->partnerStoresFilmsCount($this->getAllFilms())->get()->first()->count;
        $storesFilms = $store->partnerStoresFilms($this->getAllFilms(), $this->limit, $this->offset)->get()->keyBy('id');
        $paginator =  new lengthawarepaginator($storesFilms, $total, $this->limit, $this->page);
        return compact('store', 'paginator');
    }
}

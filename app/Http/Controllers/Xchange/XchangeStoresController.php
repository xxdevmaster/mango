<?php

namespace App\Http\Controllers\Xchange;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\Libraries\CHhelper\CHhelper;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Store;
use App\Film;
use DB;


class XchangeStoresController extends Controller
{
	private $request;
	
	private $authUser;

    private $limit = 20;

    private $offset = 0;
	
	private $page = 0;

    public function __construct(Request $request)
	{
		$this->request = $request;
		$this->authUser = Auth::user();
	}

    public function XchangeStoresShow()
    {
		$current_menu = 'Stores';
		$paginator = $this->getStores();
		
        return view('xchange.xchangeStores.xchangeStores', compact('current_menu', 'paginator'));
    }
	
	private function getStores()
	{
        $condition = [];
        if (!empty($this->request->Input('searchWord'))) {
            $filterSearch = CHhelper::filterInput($this->request->Input('searchWord'));
            $condition = ['cc_channels.title', 'LIKE "%'.$filterSearch.'%"'];
        }

        $total = Store::getXchangeStoresCountAll($condition)->first()->total;
        $stores = Store::getXchangeStores($this->limit, $this->offset, $condition)->keyBy('id');
        foreach($stores as $key => $val){
            $val = $val->setAttribute('storesFilmsCount', $val->storesFilmsCount->first()->count);
        }
        return new lengthawarepaginator($stores, $total, $this->limit, $this->page);		
	}

    /**
     *@POST("/xchange/stores/pager")
     * @Middleware("auth")
     */	
	public function pager()
	{
		if(!empty($this->request->Input('page')) && is_numeric($this->request->Input('page'))){
			$this->page = CHhelper::filterInputInt($this->request->Input('page'));
			if(($this->page - 1) != 0)
				$this->offset  = ($this->page - 1)*20;
			$paginator = $this->getStores();
			return view('xchange.xchangeStores.list_partial', compact('current_menu', 'paginator'))->render();			
		}		
	}

    /**
     *@POST("/xchange/stores/filterSearch")
     * @Middleware("auth")
     */
    public function filterSearch()
    {
        $paginator = $this->getStores();
        return view('xchange.xchangeStores.list_partial', compact('current_menu', 'paginator'))->render();
    }

    /**
     *@GET("/xchange/stores/films/{storeID}")
     * @Middleware("auth")
     */
    public function storesFilms($storeID)
    {
        $storesFilms = $this->getStoresFilms($storeID);
        return view('xchange.xchangeStores.storesFilms', compact('current_menu'), $storesFilms);
    }

    /**
     *@POST("/xchange/stores/films/pager")
     * @Middleware("auth")
     */
    public function storesFilmsPager()
    {
        $storeID = !empty($this->request->Input('storeID')) && is_numeric($this->request->Input('storeID')) ? CHhelper::filterInputInt($this->request->Input('storeID')) : false;
        $this->page = !empty($this->request->Input('page')) && is_numeric($this->request->Input('page')) ? CHhelper::filterInputInt($this->request->Input('page')) : false;
        if($storeID && $this->page){
            if(($this->page - 1) != 0)
                $this->offset  = ($this->page - 1)*20;

            $storesFilms = $this->getStoresFilms($storeID);
            return view('xchange.xchangeStores.filmList_partial',  compact('current_menu'), $storesFilms);
        }
    }

    private function getStoresFilms($storeID)
    {
        $store = Store::where('id', $storeID)->get()->first();
        $total = $store->storesFilmsCount->first()->count;
        $storesFilms = $store->storesFilms($this->limit, $this->offset)->get()->keyBy('id');
        $paginator =  new lengthawarepaginator($storesFilms, $total, $this->limit, $this->page);
        return compact('store', 'paginator');
    }
}

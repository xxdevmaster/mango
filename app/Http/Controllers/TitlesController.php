<?php

namespace App\Http\Controllers;

use Auth;
use App\Http\Requests;
use Bican\Roles\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Debug\Dumper;
use DB;
use App\Film;
use Illuminate\Http\Request;
use App\Libraries\CHhelper\CHhelper;
use App\LocaleFilms;

class TitlesController extends Controller
{
    private $offset = 0;

    private $user;

    private $accountInfo;

    private $company;

    private $store;

    private $request;

    public function __construct(Request $request)
    {
        $this->user = Auth::user();
        $this->accountInfo = $this->user->account;
        $this->company = $this->accountInfo->company;
        $this->store = $this->accountInfo->store;

        $this->request = $request;
        if(!empty($this->request->Input('offset'))){
            $this->offset = CHhelper::filterInputInt($this->request->Input('offset'));
        }
    }

    //
    public function index()
    {

        $current_menu = 'allTitles';


        //DB::enableQueryLog();
        $total = CHhelper::getAccountAllTitlesCount($this->accountInfo->platforms_id, $this->accountInfo->companies_id, '')[0]->total;
        $films = Film::getAccountAllTitles($this->accountInfo->platforms_id, $this->accountInfo->companies_id,  '', 20, $this->offset);
        $companies = $films->first()->companies;

        $pager = [
            'total' => $total,
            'limit' => 20,
            'offset' => 0,
        ];
        //print_r(DB::getQueryLog());




        //$store = $account_info->store();
        //$account_info->films();


        // print_r(DB::getQueryLog());
        //dd($store->title);
        //exit;


        //$account_info = $this->getUser();

        //$store_info = $this->getStoreInfo();
        //$company_info = $this->getCompanyInfo();






        //$store_films = $this->getStoreFilms($store_info);
        //$company_films = $this->getCompanyFilms($company_info);

        //$companies = $company_films['companies']->merge($store_films['companies']);
        //$stores = $store_films['stores']->merge($company_films['stores']);

        //$company_films = $company_films['company_films']->keyBy('id');
        //$store_films = $store_films['store_films']->keyBy('id');
        //$films = $company_films->merge($store_films);
        return view('titles.index', compact('films', 'current_menu', 'companies', 'pager'));
    }

    private function getUser()
    {
        $user_info = Auth::user();
        return $account_info = $user_info->account;
        //$account_features = $account_info->features;
    }

    private function getCompanyInfo()
    {
        $account_info = $this->getUser();
        return $account_info->company;
    }

    private function getStoreInfo()
    {
        $account_info = $this->getUser();
        return $account_info->store;
    }

    private function getCompanyFilms($company_info, $where = false, $field = null)
    {
        if($where)
            $company_films = $company_info->films()->where('cc_films.deleted', '0')->where($field, 'like', $where.'%')->get();
        else
            $company_films = $company_info->films()->where('cc_films.deleted', '0')->where('cc_films.id', 'like', '1671%')->get();

        foreach($company_films as $key=>$company_film){
            $company_film_stores = $company_films->first()->baseContract()->with('stores')->get();
            $company_film->stores = $company_film_stores->first()->stores;
            $company_film->companies = $company_film->companies()->where('fk_films_owners.type', '0')->get();
        }
        $companies = $company_film->companies;
        $stores = $company_film->stores;
        return compact('company_films', 'companies', 'stores');
    }

    private function getStoreFilms($store_info, $where = false, $field = null)
    {
        if($where)
            $store_films = $store_info->contracts()->with('films', 'stores')->where($field, 'like', $where.'%')->get();
        else
            $store_films = $store_info->contracts()->with('films', 'stores')->get();

        foreach($store_films as $key=>$store_film){
            $store_film->films->stores = $store_film->stores;
            $store_film->films->companies = $store_film->films->companies()->where('fk_films_owners.type', '0')->get();
            $store_films[$key] = $store_film->films;
        }
        $companies = $store_film->films->companies;
        $stores = $store_film->stores;

        return compact('store_films', 'companies', 'stores');
    }

    /**
     *@POST("/titles/titlesFilter")
     * @Middleware("auth")
     */
    public function titlesFilter()
    {
      /*  $account_info = $this->getUser();

        $store_info = $this->getStoreInfo();
        $company_info = $this->getCompanyInfo();
        $filtersArray = $this->request->Input('filter');
        if(is_numeric($filtersArray['search_word'])){
            $titleSearch = CHhelper::filterInputInt($filtersArray['search_word']);

            $store_films = $this->getStoreFilms($store_info, $titleSearch, 'films_id');
            $company_films = $this->getCompanyFilms($company_info, $titleSearch, 'cc_films.id');

            $company_films = $company_films['company_films']->keyBy('id');
            $store_films = $store_films['store_films']->keyBy('id');

            $films = $company_films->merge($store_films);
            if(count($films) === 0)
                return [
                    'error' => '1',
                    'message' => 'No Searching Result'
                ];
            else
                return view('titles.partials.list', compact('films'));
        }elseif(empty($filtersArray['search_word']))){
            $store_films = $this->getStoreFilms($store_info);
            $company_films = $this->getCompanyFilms($company_info);

            $films = $company_films['company_films']->merge($store_films['store_films']);
            if(count($films) === 0)
                return [
                    'error' => '1',
                    'message' => 'No Searching Result'
                ];
            else
                return view('titles.partials.list', compact('films'));
        }
        else{
            $titleSearch = CHhelper::filterInput($filtersArray['search_word']);

            $store_films = $this->getStoreFilms($store_info, $titleSearch, 'title')->join();
            $company_films = $this->getCompanyFilms($company_info, $titleSearch, 'cc_films.title');
            //$localeFilms = LocaleFilms::join('cc_films', 'cc_films.id', '=', 'locale_films.films_id')->where('locale_films.title', 'like', $titleSearch.'%')->get();
            //dd($localeFilms);
            $films = $company_films->merge($store_films);
            if(count($films) === 0)
                return [
                    'error' => '1',
                    'message' => 'No Searching Result'
                ];
            else
                return view('titles.partials.list', compact('films'));
        }*/
    }

    /**
     *@POST("/titles/pager")
     * @Middleware("auth")
     */
    public function pager()
    {
        $this->offset = CHhelper::filterInputInt($this->request->Input('pager'));
        $total = CHhelper::getAccountAllTitlesCount($this->accountInfo->platforms_id, $this->accountInfo->companies_id, '')[0]->total;
        $films = Film::getAccountAllTitles($this->accountInfo->platforms_id, $this->accountInfo->companies_id,  '', 20, $this->offset);
        $companies = $films->first()->companies;

        $pager = [
            'total' => $total,
            'limit' => 20,
            'offset' => $this->offset
        ];

        return view('titles.partials.list', compact('films', 'current_menu', 'companies', 'pager'));
    }
}

<?php

namespace App\Http\Controllers;

use Auth;
use App\Http\Requests;
use Bican\Roles\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Debug\Dumper;


use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;


use DB;
use App\Film;
use App\AllLocales;
use App\Company;
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

    private $total;

    public function __construct(Request $request)
    {
        $this->user = Auth::user();
        $this->accountInfo = $this->user->account;
        //$this->company = $this->accountInfo->company;
        //$this->store = $this->accountInfo->store;
        $this->total = $this->getTotal($this->accountInfo->platforms_id, $this->accountInfo->companies_id);
        $this->request = $request;
        if(!empty($this->request->Input('offset'))){
            $this->offset = CHhelper::filterInputInt($this->request->Input('offset'));
        }
    }

    //
    public function index()
    {

        $current_menu = 'All Titles';

        //dd($this->accountInfo->store->contracts->first());
        //DB::enableQueryLog();
        $films = Film::getAccountAllTitles($this->accountInfo->platforms_id, $this->accountInfo->companies_id, '', 20, $this->offset)->keyBy('id');
        $companies = $films->first()->companies->keyBy('id');

        /*foreach($films AS $film){
            $film->companies = $film->companies;
        }
        dd($film->companies);
        dd($films->first()->locales);*/
        foreach($films AS $film){
            $film->stores = $film->baseContract->stores->keyBy('id');
        }

        $allLocales = $this->getAllLocale();

        //print_r(DB::getQueryLog());

		$paginator = new LengthAwarePaginator($films, $this->total, 20, 0);
		//dd($paginator->perPage());

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
        return view('titles.index', compact('films', 'current_menu', 'allLocales', 'companies', 'paginator'));
    }

    private function getAllLocale()
    {
        $allLocale = AllLocales::select('title', 'code')->get()->toArray();

        if(is_array($allLocale) && count($allLocale) > 0){
            foreach($allLocale as $val) {
                $allLocales[$val['code']] = $val['title'];
            }
        }
        return $allLocales;
    }

    /**
     *@POST("/titles/getCP")
     * @Middleware("auth")
     */
    public function getCP()
    {
        if(!empty($this->request->Input('inputToken')))
            $token = CHhelper::filterInput($this->request->Input('inputToken'));
        return Company::where('deleted', '0')->where('title', 'like', $token.'%')->get();
    }

    private function getTotal($cp, $pl)
    {
        return CHhelper::getAccountAllTitlesCount($cp, $pl)[0]->total;
    }

    /**
     *@POST("/titles/titlesFilter")
     * @Middleware("auth")
     */
    public function titlesFilter()
    {
        $searchword = '';
        $field = [];
        $cp = 0;
        $pl = 0;

        if(!empty($this->request->input('filter')['searchword']) || !empty($this->request->input('filter')['cp']) || !empty($this->request->input('filter')['pl']) ){
            $filter = $this->request->input('filter');

            if(!empty($filter['searchword'])){
                if(is_numeric($filter['searchword'])){
                    $searchword = chhelper::filterinputint($filter['searchword']);
                    $field = ['and cc_films.id like', "'$searchword%'"];
                }else{
                    $searchword = chhelper::filterinput($filter['searchword']);
                    $field = ['and cc_films.title like', "'$searchword%'"];
                }
            }

            if(!empty($filter['cp']) && is_numeric($filter['cp'])){
                $cp = chhelper::filterinputint($filter['cp']);
            }

            if(!empty($filter['pl']) && is_numeric($filter['pl'])){
                $pl = chhelper::filterinputint($filter['pl']);
            }

            //$films = film::getaccountalltitles($cp, $pl, $field, 20, $this->offset)->keyby('id');

            $this->offset = 0;

            $films = film::getAccountAllTitles($this->accountInfo->platforms_id, $this->accountInfo->companies_id, $field, 20, $this->offset)->keyby('id');
            //$companies = $films->first()->companies->keyby('id');
            foreach($films as $film){
                $film->stores = $film->basecontract->stores->keyby('id');
            }

            $this->total = $this->getTotal($cp, $pl);

            $paginator = new lengthawarepaginator($films, $this->total, 20, 0);

            return view('titles.partials.list', compact('films', 'paginator'));

        }else{
            $this->offset = 0;
            $films = film::getAccountAllTitles($this->accountInfo->platforms_id, $this->accountInfo->companies_id,  '', 20, $this->offset)->keyby('id');
            //$companies = $films->first()->companies->keyby('id');
            foreach($films as $film){
                $film->stores = $film->basecontract->stores->keyby('id');
            }

			$paginator = new lengthawarepaginator($films, $this->total, 20, 0);

            return view('titles.partials.list', compact('films', 'paginator'));
        }
        /*  $account_info = $this->getuser();

          $store_info = $this->getstoreinfo();
          $company_info = $this->getcompanyinfo();
          $filtersarray = $this->request->input('filter');
          if(is_numeric($filtersarray['search_word'])){
              $titlesearch = chhelper::filterinputint($filtersarray['search_word']);

              $store_films = $this->getstorefilms($store_info, $titlesearch, 'films_id');
              $company_films = $this->getcompanyfilms($company_info, $titlesearch, 'cc_films.id');

              $company_films = $company_films['company_films']->keyby('id');
              $store_films = $store_films['store_films']->keyby('id');

              $films = $company_films->merge($store_films);
              if(count($films) === 0)
                  return [
                      'error' => '1',
                      'message' => 'no searching result'
                  ];
              else
                  return view('titles.partials.list', compact('films'));
          }elseif(empty($filtersarray['search_word']))){
              $store_films = $this->getstorefilms($store_info);
              $company_films = $this->getcompanyfilms($company_info);

              $films = $company_films['company_films']->merge($store_films['store_films']);
              if(count($films) === 0)
                  return [
                      'error' => '1',
                      'message' => 'no searching result'
                  ];
              else
                  return view('titles.partials.list', compact('films'));
          }
          else{
              $titlesearch = chhelper::filterinput($filtersarray['search_word']);

              $store_films = $this->getstorefilms($store_info, $titlesearch, 'title')->join();
              $company_films = $this->getcompanyfilms($company_info, $titlesearch, 'cc_films.title');
              //$localefilms = localefilms::join('cc_films', 'cc_films.id', '=', 'locale_films.films_id')->where('locale_films.title', 'like', $titlesearch.'%')->get();
              //dd($localefilms);
              $films = $company_films->merge($store_films);
              if(count($films) === 0)
                  return [
                      'error' => '1',
                      'message' => 'no searching result'
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
        $page = CHhelper::filterInputInt($this->request->Input('page'));
		if(($page - 1) != 0)
			$this->offset  = ($page - 1)*20;
		else
			$this->offset  = 0;
        $films = Film::getAccountAllTitles($this->accountInfo->platforms_id, $this->accountInfo->companies_id,  '', 20, $this->offset)->keyBy('id');
        //$companies = $films->first()->companies->keyBy('id');
        foreach($films AS $film){
            $film->stores = $film->baseContract->stores->keyBy('id');
        }

		$paginator = new LengthAwarePaginator($films, $this->total, 20, $page);
		
        return view('titles.partials.list', compact('films', 'paginator'));
    }
}

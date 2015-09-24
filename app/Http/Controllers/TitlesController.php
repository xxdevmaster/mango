<?php

namespace App\Http\Controllers;

use Auth;
use App\Http\Requests;
use Illuminate\Support\Collection;
use Illuminate\Support\Debug\Dumper;
use DB;

class TitlesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //
    public function index(){

        $current_menu = 'allTitles';

        DB::enableQueryLog();

        $user_info = Auth::user();
       // (new Dumper)->dump($user_info->toArray());

        $account_info = $user_info->account;
        //(new Dumper)->dump($account_info->toArray());

        $company_info = $account_info->company;
        //(new Dumper)->dump($company_info->toArray());

        $company_films = $company_info->films()->where('cc_films.deleted', '0')->get();


        $store_info = $account_info->store;
        //(new Dumper)->dump($store_info->toArray());

        $store_films = $store_info->contracts()->with('films', 'stores')->get();

        foreach($company_films as $key=>$company_film){
            $company_film_stores = $company_films->first()->baseContract()->with('stores')->get();

            $company_film->stores = $company_film_stores->first()->stores;
            $company_film->companies = $company_film->companies()->where('fk_films_owners.type', '0')->get();
        }


        foreach($store_films as $key=>$store_film){
            $store_film->films->stores = $store_film->stores;
            $store_film->films->companies = $store_film->films->companies()->where('fk_films_owners.type', '0')->get();
            $store_films[$key] = $store_film->films;
            //unset($store_films[$key]->stores);
        }

        $company_films = $company_films->keyBy('id');
        $store_films = $store_films->keyBy('id');
        $films = $company_films->merge($store_films);
        dd($films);
        //$store_films = $store_films->toArray();
        //(new Dumper)->dump($store_films[0]['stores']);
        //(new Dumper)->dump($store_films[0]['films']);

        //$queries = DB::getQueryLog();

        //(new Dumper)->dump($last_query = end($queries));

        return view('titles.list', compact('store_films', 'current_menu'));
    }
}

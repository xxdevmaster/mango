<?php

namespace App\Http\Controllers;

use Auth;
use App\Http\Requests;
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
       //(new Dumper)->dump($user_info->toArray());

        $account_info = $user_info->account;
        //(new Dumper)->dump($account_info->toArray());

        $company_info = $account_info->company;
        //(new Dumper)->dump($company_info->toArray());

        $films = $company_info->films()->where('cc_films.deleted', '0')->get();
        //(new Dumper)->dump($films->toArray());

        $film_cp = $films->first()->companies()->where('fk_films_owners.type', '0')->get();
        //(new Dumper)->dump($film_cp->toArray());



        $store_info = $account_info->store;
        //(new Dumper)->dump($store_info->toArray());

        $store_films = $store_info->contracts()->with('films', 'stores')->get();
        //$store_films = $store_films->toArray();
        //(new Dumper)->dump($store_films[0]['stores']);
        //(new Dumper)->dump($store_films[0]['films']);

        //$queries = DB::getQueryLog();

        //(new Dumper)->dump($last_query = end($queries));

        return view('titles.list', compact('store_films', 'current_menu'));
    }
}

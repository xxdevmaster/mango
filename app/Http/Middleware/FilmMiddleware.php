<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use App\Libraries\CHhelper\CHhelper;
use App\Film;

class FilmMiddleware
{
    private $filmId;

    private $film;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $current_menu = '';
		   //dd();
        if(!empty($request->filmId))
            $this->filmId = CHhelper::filterInputInt($request->filmId);
        else
            $this->filmId = CHhelper::filterInputInt($request->header('filmId'));

		
        if(!is_numeric($this->filmId) || $this->filmId == 0)
            return view('errors.404', compact('current_menu'));

        $userInfo = Auth::user();

        $accountInfo = $userInfo->account;

        /*$accountFeatures = $accountInfo->features;

        $companyInfo = $accountInfo->company;

        $companyFilms = $companyInfo->films()->where('cc_films.deleted', '0')->get();

        $film = $companyInfo->films()->where( 'cc_films.id', $this->filmId)->get();

        if(count($film) != 0) {
            $this->film = $film[0];
        }else {
            $storeInfo = $accountInfo->store;
            $storeFilms = $storeInfo->contracts()->with( 'films', 'stores' )->where( 'films_id', $this->filmId )->get();
            foreach($storeFilms as $storeFilm){
                $this->film = $storeFilm->films;
            }
        }*/
        $this->film = Film::getAccountAllTitles($accountInfo->platforms_id, $accountInfo->companies_id, ['AND cc_films.id', '=', $this->filmId])->first();

        if(count($this->film) === 0)
            return view('errors.550');


        $request->merge(array("film" => $this->film, 'filmId' => $this->filmId));
        return $next($request);
    }
}

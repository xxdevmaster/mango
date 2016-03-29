<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use App\Libraries\CHhelper\CHhelper;
use App\Film;

class FilmMiddleware
{
    private $filmID;

    private $film;

    private $authUser;

    private $accountID;

    private $storeID;

    private $companyID;	
	
    public function __construct()
    {
        $this->authUser = Auth::user();
        $this->accountID = $this->authUser->account->id;
        $this->storeID = $this->authUser->account->platforms_id;
        $this->companyID = $this->authUser->account->companies_id;
    }	
	
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!empty($request->filmID))
            $this->filmID = CHhelper::filterInputInt($request->filmID);
        else
            $this->filmID = CHhelper::filterInputInt($request->header('filmID'));

        if(!is_numeric($this->filmID) || $this->filmID == 0)
            return view('errors.404', compact('current_menu'));

        $userInfo = Auth::user();

        $accountInfo = $userInfo->account;

		
		$this->film = $this->getFilm();

        if($this->film->isEmpty())
            return view('errors.550');


        $request->merge(array("film" => $this->film, 'filmID' => $this->filmID));
        return $next($request);
    }

    /**
     * Get film.
     * @return collection
    */	
	private function getFilm()
	{
        if( $this->storeID > 0 && $this->companyID > 0)
        {
            $union = Film::distinct()->join('fk_films_owners', 'fk_films_owners.films_id', '=', 'cc_films.id')
                                    ->where('fk_films_owners.owner_id', $this->companyID)
                                    ->where('fk_films_owners.type', 0)
                                    ->where('cc_films.deleted', 0)
									->where('cc_films.id', $this->filmID);

            return Film::distinct()->join('cc_base_contracts', 'cc_base_contracts.films_id', '=', 'cc_films.id')
                                    ->join('cc_channels_contracts', 'cc_channels_contracts.bcontracts_id', '=', 'cc_base_contracts.id')
                                    ->where('cc_channels_contracts.channel_id', $this->storeID)
                                    ->where('cc_films.deleted', 0)
									->where('cc_films.id', $this->filmID)
									->union($union->select('cc_films.*'))
									->select('cc_films.*')->get();
        }
        elseif( $this->storeID > 0)
        {
            return Film::join('cc_base_contracts', 'cc_base_contracts.films_id', '=', 'cc_films.id')
                              ->join('cc_channels_contracts', 'cc_channels_contracts.bcontracts_id', '=', 'cc_base_contracts.id')
                              ->where('cc_channels_contracts.channel_id', $this->storeID)
                              ->where('cc_films.deleted', 0)
                              ->where('cc_films.id', $this->filmID)
							  ->select('cc_films.*')->get();


        }
        elseif( $this->companyID > 0)
        {
            return Film::join('fk_films_owners', 'fk_films_owners.films_id', '=', 'cc_films.id')
                            ->where('fk_films_owners.owner_id', $this->companyID)
                            ->where('fk_films_owners.type', 0)
                            ->where('cc_films.deleted', 0)
                            ->where('cc_films.id', $this->filmID)
							->select('cc_films.*')->get();
        }		
	}
}

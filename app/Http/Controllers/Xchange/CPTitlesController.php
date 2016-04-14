<?php

namespace App\Http\Controllers\Xchange;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\Libraries\CHhelper\CHhelper;
use App\Models\Vaults;
use App\Models\CronJobs;
use App\Countries;
use App\Film;
use App\FilmOwners;
use App\Account;
use App\User;
use App\Store;
use App\Libraries\MandrillService\Mandrill;

class CPTitlesController extends Controller
{
    private $request;

    private $authUser;

	private $storeID;

	private $companyID;

	private $company;

	private $limit = 20;

	private $offset = 0;

	private $page = 0;

	private $orderBy = 'id';

	private $orderType = 'asc';

	private $filterStoreID = false;

	private $searchWord = false;

	private $stores = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->authUser = Auth::user();
		$this->storeID = $this->authUser->account->platforms_id;
		$this->company = $this->authUser->account->company;
		$this->companyID = $this->authUser->account->companies_id;
    }

    public function CPTitlesShow()
    {
		$allFilmsIDS = $this->getAllFilmsIDS();
		$data = $this->getFilms();
		$stores = $this->getStores($allFilmsIDS);
        return view('xchange.CPTitles.CPTitles', compact('stores'), $data);
    }

	/**
	 * Get all films, pagintion, films stores.
	 * @return array
	 */
	public function getFilms()
	{
		$allFilmsIDS = $this->getAllFilmsIDS();
		$films = Film::join('fk_films_owners', 'fk_films_owners.films_id', '=', 'cc_films.id')
						->where('fk_films_owners.owner_id', $this->companyID)
						->where('fk_films_owners.type', 0)
						->where('cc_films.deleted', 0);


		$filter = (!empty($this->request->input('filter')) && is_array($this->request->input('filter'))) ? $this->request->input('filter') : false;
		if($filter)
		{
			$this->searchWord = !empty($filter['searchWord']) ? CHhelper::filterInput($filter['searchWord']) : false;
			$this->filterStoreID = (!empty($filter['pl']) && is_numeric($filter['pl'])) ? CHhelper::filterInputInt($filter['pl']) : false;

			if(!empty($filter['order']) && ($filter['order'] == 'id' || $filter['order'] == 'title'))
				$this->orderBy = CHhelper::filterInput($filter['order']);
			if(!empty($filter['orderType']) && ($filter['orderType'] == 'asc' || $filter['orderType'] == 'desc'))
				$this->orderType = CHhelper::filterInput($filter['orderType']);
		}

		if(!empty($filter['vaultStatus']))
		{
			if($filter['vaultStatus'] == 1)
			{
				$films->join('cc_vaults', function ($join) {
					$join->on('fk_films_owners.films_id', '=', 'cc_vaults.films_id')
						->where('cc_vaults.companies_id','=', $this->companyID);
				});
			}else {
				$films->leftJoin('cc_vaults', function ($join) {
					$join->on('fk_films_owners.films_id', '=', 'cc_vaults.films_id')
						->where('cc_vaults.companies_id','=', $this->companyID);
				})->whereNull('cc_vaults.id');
			}
		}else {
			$films->leftJoin('cc_vaults', function ($join) {
				$join->on('fk_films_owners.films_id', '=', 'cc_vaults.films_id')
					->where('cc_vaults.companies_id','=', $this->companyID);
			});
		}


		if($this->searchWord)
		{
			$films->where(function($query){
				$query->where('cc_films.title', 'like', "$this->searchWord%")
					->orWhere('cc_films.id', 'like', "$this->searchWord%");
			});
		}

		if($this->filterStoreID)
		{
			$storeFilmsIDS = FilmOwners::where('owner_id', $this->filterStoreID)->where('type', 1)->lists('films_id');
			$films = $films->whereIn('cc_films.id', $storeFilmsIDS);
		}

		$filmsTotal = $films->count();
		$films = $films->select('cc_films.*', 'cc_films.id AS id', 'cc_vaults.id AS vaultID', 'cc_vaults.delete_dt')->orderBY($this->orderBy, $this->orderType)->limit($this->limit)->skip($this->offset)->get()->keyBy('id');

		$films = new LengthAwarePaginator($films, $filmsTotal, $this->limit, $this->page);
		$filmStores = $this->getFilmStores($allFilmsIDS);
		$orderBy = $this->orderBy;
		$orderType = $this->orderType;

		return compact('films', 'filmStores', 'orderBy', 'orderType');
	}

	/**
	 * Get all films ids.
	 * @return collection
	 */
	private function getAllFilmsIDS()
	{
		return $this->company->films('cc_films.id')->lists('id')->toArray();
	}

	/**
	 * Get stores.
	 * @param  array or collection  $filmsIDS
	 * @return collection
	 */
	private function getStores($filmsIDS)
	{
		return Store::join('fk_films_owners', 'cc_channels.id', '=', 'fk_films_owners.owner_id')
					->where('fk_films_owners.type', 1)
					->where('cc_channels.title', '<>', '')
					->whereIn('fk_films_owners.films_id', $filmsIDS)
					->groupBy('cc_channels.id')
					->select('cc_channels.title as title', 'cc_channels.id as id')->lists('title', 'id');
    }

	/**
	 * Get film stores.
	 * @param  array or collection  $filmsIDS
	 * @return array
	 */
	public function getFilmStores($films)
	{
		$filmStores = Store::distinct()->join('fk_films_owners', 'cc_channels.id', '=', 'fk_films_owners.owner_id')
					->where('type', '1')->whereIn('fk_films_owners.films_id', $films)
					->select('cc_channels.title', 'fk_films_owners.films_id')->get()
					->lists('title', 'films_id');

		foreach($filmStores as $storeID => $storeTitle)
			$this->stores[$storeID][] = $storeTitle;

		return $this->stores;
	}

	/**
	 *@POST("/CPTitles/titlesFilter")
	 * @Middleware("auth")
	 */
	public function titlesFilter()
	{
		$data = $this->getFilms();
		return view('xchange.CPTitles.list', $data)->render();
	}

    /**
     *@POST("/CPTitles/soloActAddToVault")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function soloActAddToVault()
    {
        Vaults::create([
            'films_id' => $this->request->filmID ,
            'companies_id' => $this->companyID
        ]);

		$data = $this->getFilms();
		return view('xchange.CPTitles.list', $data)->render();
    }    
	
	/**
     *@POST("/CPTitles/soloActDeleteFromVault")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function soloActDeleteFromVault()
    {
		$vault = Vaults::where('films_id', $this->request->filmID)->where('companies_id', $this->companyID)->select('cc_vaults.id')->get();
		$vaultID = $vault->first()->id;
		$channelsVaults = $vault->first()->channelsVaults;
		
		$i = 0;
		$channelsConnectedToThisFilm = array();
		
		foreach($channelsVaults as $key => $val){
			$i++;
			$channelsConnectedToThisFilm[]=array('channel_id' => $val->channels_id, 'vault_id' => $vaultID, 'film_id' => $this->request->filmID);
		}

		if($i == 0){
			Vaults::where('films_id', $this->request->filmID)->where('companies_id', $this->companyID)->delete();
		}
		else{
			$this->notifyStoresAboutDeleting($channelsConnectedToThisFilm);
		}

		$data = $this->getFilms();
		return view('xchange.CPTitles.list', $data)->render();
    }

	/**
	 * Send email notifycation to store about deleting film of xchange.
	 * @param  array or collection  $filmsIDS
	 * @return array
	 */
	private function notifyStoresAboutDeleting($platforms)
	{
		$countries = array();
        foreach ($platforms AS $k =>$v){
			$vaultID = $v['vault_id'];
			$filmID = $v['film_id'];
            $channelID = $v['channel_id'];

            $res = Countries::join('cc_geo_contracts', 'cc_geo_contracts.countries_id', '=', 'cc_countries.id')
                ->where('cc_geo_contracts.films_id', $filmID)
                ->where('cc_geo_contracts.companies_id', $channelID)
                ->where('cc_geo_contracts.deleted', '0')
                ->get();

            foreach($res as $v){
                $countries[] = $v->country_title;
            }

            $filmInfo = Film::where('id', $filmID)->get()->first();

            $account = Account::where('platforms_id', $channelID)->get();
            $accountMails = User::where('accounts_id', $account->first()->id)
                                  ->where('cms_role', '<', '2')
                                  ->select('email', 'person', 'cms_role')
                                  ->get();
            foreach ($accountMails as  $k =>$v ){
                if (!empty($v->email)){
                    $type = $v->cms_role == 0 ? 'to' : 'cc' ;
                    $rcpts[] = array('email' => $v->email, 'name' => $v->person, 'type' => $type);
                }
            }

			$mandrill = new Mandrill("zrVZzzehpLYYFcnHkvegGw");
			$message = array(
				'subject' => 'Notification from Cinehost, Inc. ',
				'from_email' => 'noreply@cinehost.com',
				'html' => '' ,
				'to' =>  array(array('email' => 'lyov.karamyan@mail.ru', 'name' => 'Levon', 'type' => 'to'))//$rcpts
            );

			$templateName = 'VaultFilmDeleteNotifyPlatforms';
			$templateContent = array(
				array(
					'name' => 'FilmName',
					'content' => '<a href="http://pro.cinehost.com/titles/metadata/'.$filmInfo->id.'">'.$filmInfo->title.'</a>'
				),
				array(
					'name' => 'countries',
					'content' => implode(', ', $countries)
				)
			);

			$mandrill->messages->sendTemplate($templateName, $templateContent, $message);
        }		

        $today = strtotime(date("Y-m-d H:i:s"));
        $dt = date("Y-m-d H:i:s",strtotime("+48 hour", $today));
		
		Vaults::where('id', $vaultID)->update([
			'delete_dt' => $dt
		]);
		
		CronJobs::create([
			'vaults_id' => $vaultID ,
			'delete_dt' => $dt ,
			'companies_id' => $this->companyID ,
			'films_id' => $filmID
		]);	
	}
	
    /**
     *@POST("/CPTitles/bulkActAddToVault")
     * @Middleware("auth")
     */
    public function bulkActAddToVault()
    {
		if(!empty($this->request->Input('filmsNotInVault'))){
			foreach($this->request->Input('filmsNotInVault') as $key => $value){
				if($value == 'on'){
					$filmID = CHhelper::filterInputInt($key);
					Vaults::create([
						'films_id' => $filmID ,
						'companies_id' => $this->companyID
					]);
				}
			}				
		}

		$data = $this->getFilms();
		return view('xchange.CPTitles.list', $data)->render();
    }

    /**
     *@POST("/CPTitles/bulkActDeleteFromVault")
     * @Middleware("auth")
     */	
    public function bulkActDeleteFromVault()
	{    
		if(!empty($this->request->Input('filmsInVault'))){
			foreach($this->request->Input('filmsInVault') AS $k =>$v){
				if ($v=='on')
				{
					$i = 0;
					$vault = Vaults::where('films_id', $k)->where('companies_id', $this->companyID)->select('cc_vaults.id')->get();
					$vaultID = $vault->first()->id;
					
					$channelsVaults = $vault->first()->channelsVaults;

					foreach($channelsVaults as $key => $val){
						$i++;
						$channelsConnectedToThisFilm[]=array('channel_id' => $val->channels_id, 'vault_id' => $vaultID, 'film_id' => $k);
					}

					if($i==0){
						Vaults::where('films_id', $k)->where('companies_id', $this->companyID)->delete();
					}
					else{
						$this->notifyStoresAboutDeleting($channelsConnectedToThisFilm);
					}                   
				}
			}			
		}

		$data = $this->getFilms();
		return view('xchange.CPTitles.list', $data)->render();
    }

	/**
	 *@POST("/CPTitles/pager")
	 * @Middleware("auth")
	 */
	public function pager()
	{
		$this->page = (!empty($this->request->Input('page')) && is_numeric($this->request->Input('page'))) ? CHhelper::filterInputInt($this->request->Input('page')) : 0;

		if(($this->page - 1) != 0)
			$this->offset  = ($this->page - 1)*20;

		$data = $this->getFilms();
		return view('xchange.CPTitles.list', $data)->render();
	}
}

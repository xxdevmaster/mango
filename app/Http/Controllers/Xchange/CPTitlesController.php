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
		
		
		
		$getAllFilmsIDS = $this->getAllFilmsIDS();

		//$this->getStores($getAllFilmsIDS);
		
		$this->getFilmsPlatforms($getAllFilmsIDS);

		$films = $this->getFilmsCp();
		$stores = Store::getFilmStores($this->storeID, $this->companyID);
		//dd($stores);
        return view('xchange.CPTitles.CPTitles', compact('films', 'stores'));
    }

	public function getFilmsCp()
	{
        $films = $this->company->films->keyBy('id');

        $vaults = $this->company->vaults->keyBy('films_id');
        foreach($films as $key => &$val){
            foreach($vaults as $k => $v){
                if($key == $k){
                    $val->setattribute('delete_dt', $v->delete_dt);
                    $val->setattribute('V_ID', $v->id);
                }
            }
        }

		$filmsTotal = $this->company->filmsTotal->first()->count;

		return new LengthAwarePaginator($films, $filmsTotal, $this->limit, $this->page);
	}

	private function getAllFilmsIDS()
	{		
		return $this->company->films('cc_films.id')->lists('id')->toArray();
	}

    private function getStores($filmsIDS) 
	{
		$x = Store::join('fk_films_owners', 'cc_channels.id', '=', 'fk_films_owners.owner_id')
					->where('fk_films_owners.type', 1)
					->where('cc_channels.title', '<>', '')
					->whereIn('fk_films_owners.films_id', $filmsIDS)
					->groupBy('cc_channels.id')->get();
					
		dd($x);			
        $q ="SELECT cc_channels.id,cc_channels.title  "
                 . "FROM cc_channels JOIN fk_films_owners ON cc_channels.id=fk_films_owners.owner_id "
                 . "WHERE fk_films_owners.films_id IN ('".implode("','",$films)."') AND fk_films_owners.type=1 AND cc_channels.title<>'' GROUP BY cc_channels.id ";
        $res= G('DB')->query($q);
        while($row = $res->fetch(PDO::FETCH_ASSOC)){
            $out[$row['id']] = $row['title']; 
        }
        return $out;
    }
	
    private function getFilmStores($films) 
	{
        $res= G('DB')->query($q1="
                SELECT DISTINCT cc_channels.title,fk_films_owners.films_id
                FROM cc_channels 
                INNER JOIN fk_films_owners ON cc_channels.id=fk_films_owners.owner_id WHERE 
                type=1  AND fk_films_owners.films_id IN ('".implode("','",$films)."')"); 
        while($row = $res->fetch(PDO::FETCH_OBJ))
            $pls[$row->films_id][] = $row->title;
        return $pls;
    }	
	
	public function getFilmsPlatforms($films)
	{
		return Store::join('fk_films_owners', 'cc_channels.id', '=', 'fk_films_owners.owner_id')
					->where('type', '1')->whereIn('fk_films_owners.films_id', $films)
					->select('cc_channels.title', 'fk_films_owners.films_id')->get()
					->lists('title', 'films_id')->toArray();

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
		
		$films = $this->getFilmsCp();
		$stores = Store::getFilmStores($this->storeID, $this->companyID);
		return view('xchange.CPTitles.list_partial', compact('films', 'stores'))->render();
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
			$this->notifyPlatformsAboutDeleting($channelsConnectedToThisFilm);
		} 
		
		$films = $this->getFilmsCp();
		$stores = Store::getFilmStores($this->storeID, $this->companyID);
		return view('xchange.CPTitles.list_partial', compact('films', 'stores'))->render();
    }

	private function notifyPlatformsAboutDeleting($platforms)
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
		
		$films = $this->getFilmsCp();
		$stores = Store::getFilmStores($this->storeID, $this->companyID);
		return view('xchange.CPTitles.list_partial', compact('films', 'stores'))->render();
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
						$this->notifyPlatformsAboutDeleting($channelsConnectedToThisFilm);
					}                   
				}
			}			
		}
		$films = $this->getFilmsCp();
		$stores = Store::getFilmStores($this->storeID, $this->companyID);

		return view('xchange.CPTitles.list_partial', compact('films', 'stores'))->render();
    }
	
}

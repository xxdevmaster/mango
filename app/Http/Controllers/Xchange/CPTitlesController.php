<?php

namespace App\Http\Controllers\Xchange;

use Illuminate\Http\Request;

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

	private $platformsID;

	private $countriesID;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->authUser = Auth::user();
		$this->platformsID = $this->authUser->account->platforms_id;
		$this->companiesID = $this->authUser->account->companies_id;
    }

    public function CPTitlesShow()
    {
		$films = $this->getFilmsCp();
		$stores = Store::getFilmStores($this->platformsID, $this->companiesID);
        return view('xchange.CPTitles.CPTitles', compact('films', 'stores'));
    }

	public function getFilmsCp()
	{
        $company = $this->authUser->account->company;
        $company_ID = $this->authUser->account->company->id;


        $films = $company->films->keyBy('id');
        $vaults = $this->authUser->account->company->vaults->keyBy('films_id');
        foreach($films as $key => &$val){
            foreach($vaults as $k => $v){
                if($key == $k){
                    $val->setattribute('delete_dt', $v->delete_dt);
                    $val->setattribute('V_ID', $v->id);
                }
            }
        }
		return $films;
	}
	
    /**
     *@POST("/CPTitles/soloActAddToVault")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function soloActAddToVault()
    {
        $company_ID = $this->authUser->account->company->id;

        Vaults::create([
            'films_id' => $this->request->filmId ,
            'companies_id' => $company_ID
        ]);
		
		$films = $this->getFilmsCp();
		$stores = Store::getFilmStores($this->platformsID, $this->companiesID);
		return view('xchange.CPTitles.list_partial', compact('films', 'stores'))->render();
    }    
	
	/**
     *@POST("/CPTitles/soloActDeleteFromVault")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function soloActDeleteFromVault()
    {
        $company_ID = $this->authUser->account->company->id;

		$vault = Vaults::where('films_id', $this->request->filmId)->where('companies_id', $company_ID)->select('cc_vaults.id')->get();
		$vault_ID = $vault->first()->id;

		//$res= G('DB')->query($q = "SELECT * FROM fk_channels_vaults WHERE vaults_id = '".$vaultId->id."'");$qq[]=$q;
		$channelsVaults = $vault->first()->channelsVaults;
		
		$i = 0;
		$channelsConnectedToThisFilm = array();
		
		foreach($channelsVaults as $key => $val){
			$i++;
			$channelsConnectedToThisFilm[]=array('channel_id' => $val->channels_id, 'vault_id' => $vault_ID, 'film_id' => $this->request->filmId);
		}

		if($i == 0){
			Vaults::where('films_id', $this->request->filmId)->where('companies_id', $company_ID)->delete();
		}
		else{
			$this->notifyPlatformsAboutDeleting($channelsConnectedToThisFilm);
		} 
		
		$films = $this->getFilmsCp();
		$stores = Store::getFilmStores($this->platformsID, $this->companiesID);
		return view('xchange.CPTitles.list_partial', compact('films', 'stores'))->render();
    }

	private function notifyPlatformsAboutDeleting($platforms)
	{
        $company_ID = $this->authUser->account->company->id;
		$countries = array();
        foreach ($platforms AS $k =>$v){
			$vault_ID = $v['vault_id'];
			$film_ID = $v['film_id'];
            $channel_ID = $v['channel_id'];

            $res = Countries::join('cc_geo_contracts', 'cc_geo_contracts.countries_id', '=', 'cc_countries.id')
                ->where('cc_geo_contracts.films_id', $film_ID)
                ->where('cc_geo_contracts.companies_id', $channel_ID)
                ->where('cc_geo_contracts.deleted', '0')
                ->get();

            foreach($res as $v){
                $countries[] = $v->country_title;
            }

            $filmInfo = Film::where('id', $film_ID)->get()->first();

            $account = Account::where('platforms_id', $channel_ID)->get();
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

			$template_name = 'VaultFilmDeleteNotifyPlatforms';
			$template_content = array(
				array(
					'name' => 'FilmName',
					'content' => '<a href="http://pro.cinehost.com/titles/metadata/'.$filmInfo->id.'">'.$filmInfo->title.'</a>'
				),
				array(
					'name' => 'countries',
					'content' => implode(', ', $countries)
				)
			);

			$mandrill->messages->sendTemplate($template_name, $template_content, $message);
        }		
		
		$company_ID = $this->authUser->account->company->id;
        $today = strtotime(date("Y-m-d H:i:s"));
        $dt = date("Y-m-d H:i:s",strtotime("+48 hour", $today));
		
		Vaults::where('id', $vault_ID)->update([
			'delete_dt' => $dt
		]);
		
		CronJobs::create([
			'vaults_id' => $vault_ID ,
			'delete_dt' => $dt ,
			'companies_id' => $company_ID ,
			'films_id' => $film_ID
		]);	
	}
	
    /**
     *@POST("/CPTitles/bulkActAddToVault")
     * @Middleware("auth")
     */
    public function bulkActAddToVault()
    {
		if(!empty($this->request->Input('filmsNotInVault'))){
			$company_ID = $this->authUser->account->company->id;
			foreach($this->request->Input('filmsNotInVault') as $key => $value){
				if($value == 'on'){
					$films_ID = CHhelper::filterInputInt($key);
					Vaults::create([
						'films_id' => $films_ID ,
						'companies_id' => $company_ID
					]);
				}
			}				
		}
		
		$films = $this->getFilmsCp();
		$stores = Store::getFilmStores($this->platformsID, $this->companiesID);
		return view('xchange.CPTitles.list_partial', compact('films', 'stores'))->render();
    }

    /**
     *@POST("/CPTitles/bulkActDeleteFromVault")
     * @Middleware("auth")
     */	
    public function bulkActDeleteFromVault()
	{    
		if(!empty($this->request->Input('filmsInVault'))){
			$company_ID = $this->authUser->account->company->id; 
			foreach($this->request->Input('filmsInVault') AS $k =>$v){
				if ($v=='on')
				{
					$i = 0;
					$vault = Vaults::where('films_id', $k)->where('companies_id', $company_ID)->select('cc_vaults.id')->get();
					$vault_ID = $vault->first()->id;
					
					$channelsVaults = $vault->first()->channelsVaults;

					foreach($channelsVaults as $key => $val){
						$i++;
						$channelsConnectedToThisFilm[]=array('channel_id' => $val->channels_id, 'vault_id' => $vault_ID, 'film_id' => $k);
					}

					if($i==0){
						Vaults::where('films_id', $k)->where('companies_id', $company_ID)->delete();
					}
					else{
						$this->notifyPlatformsAboutDeleting($channelsConnectedToThisFilm);
					}                   
				}
			}			
		}
		$films = $this->getFilmsCp();
		$stores = Store::getFilmStores($this->platformsID, $this->companiesID);
		return view('xchange.CPTitles.list_partial', compact('films'), 'stores')->render();
    }
	
}

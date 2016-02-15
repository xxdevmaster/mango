<?php
namespace App\Http\Controllers\TitleManagement;

use Illuminate\Http\Request;


use Bican\Roles\Models\Permission;
use Bican\Roles\Models\Role;


use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Libraries\CHhelper\CHhelper;
use App\Film;
use App\Company;
use DB;
use App\Models\GeoTemplates;
use App\FilmOwners;
use App\BaseContracts;
use App\Models\ContractsShares;
use App\Models\GeoContracts;
use App\Countries;

class RightsController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
		$this->middleware('rightsPermission');
		$this->request = $request;
    }

    public function rightsShow()
    {

        $current_menu = '';
        $film = $this->request->film;
		$cp = $this->request->film->companies->keyBy('id');


		$authUser = Auth::user();
		$authCompanyID = $authUser->account->companies_id;
		$authPlatformID = $authUser->account->platform_id;

		$geoTemplates = GeoTemplates::where('deleted', '0')->get()->keyBy('id');
		$countries = $this->request->film->geoCountries->where('companies_id',$authCompanyID)->keyBy('countries_id');
		$converted = $this->getNewGeoTemplateData($geoTemplates->first()->id, $countries);
		$contractShareInfo = DB::table('cc_contracts_shares')
			->join('cc_channels_contracts', 'cc_channels_contracts.id', '=', 'cc_contracts_shares.contracts_id')
			->join('cc_base_contracts', 'cc_base_contracts.id', '=', 'cc_channels_contracts.bcontracts_id')
			->where('cc_channels_contracts.channel_id', $authPlatformID)
			->where('cc_base_contracts.films_id', '341')
			->where('cc_contracts_shares.companies_id', $authCompanyID)->select('cc_contracts_shares.*')->get();
		if(empty($contractShareInfo)){
			$contractId = '159';
			ContractsShares::create([
				'contracts_id' => $contractId ,
				'companies_id' => $authCompanyID
			]);
			$contractShareInfo = DB::table('cc_contracts_shares')
				->join('cc_channels_contracts', 'cc_channels_contracts.id', '=', 'cc_contracts_shares.contracts_id')
				->join('cc_base_contracts', 'cc_base_contracts.id', '=', 'cc_channels_contracts.bcontracts_id')
				->where('cc_channels_contracts.channel_id', $authPlatformID)
				->where('cc_base_contracts.films_id', '341')
				->where('cc_contracts_shares.companies_id', $authCompanyID)->select('cc_contracts_shares.*')->get();
		}
		$contractShareInfo = $this->request->film->basecontract;

		$rightsPermission = $this->request->rightsPermission;

        return view('titles.titleManagement.rights.rights', compact('current_menu', 'film', 'cp', 'converted', 'countries', 'authCompanyID', 'contractShareInfo', 'rightsPermission'));
    }

    /**
     *@POST("/titles/rights/getChangeCPPL")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function getChangeCPPL()
    {
		$type = !empty($this->request->Input('type'))?$this->request->Input('type'):'';
			if($type === 'CP') {
				$authUser = Auth::user();
				$authCompanyID = $authUser->account->companies_id;
				$authPlatformID = $authUser->account->platform_id;

				$type = CHhelper::filterInput($this->request->Input('type'));
				$film = $this->request->film;

				$geoTemplates = GeoTemplates::where('deleted', '0')->get()->keyBy('id');
				$countries = $this->request->film->geoCountries->where('companies_id',$authCompanyID)->keyBy('countries_id');
				$converted = $this->getNewGeoTemplateData($geoTemplates->first()->id, $countries);

				$contractShareInfo = DB::table('cc_contracts_shares')
					->join('cc_channels_contracts', 'cc_channels_contracts.id', '=', 'cc_contracts_shares.contracts_id')
					->join('cc_base_contracts', 'cc_base_contracts.id', '=', 'cc_channels_contracts.bcontracts_id')
					->where('cc_channels_contracts.channel_id', $authPlatformID)
					->where('cc_base_contracts.films_id', '341')
					->where('cc_contracts_shares.companies_id', $authCompanyID)->select('cc_contracts_shares.*')->get();
				if(empty($contractShareInfo)){
					$contractId = '159';
					ContractsShares::create([
						'contracts_id' => $contractId ,
						'companies_id' => $authCompanyID
					]);
					$contractShareInfo = DB::table('cc_contracts_shares')
						->join('cc_channels_contracts', 'cc_channels_contracts.id', '=', 'cc_contracts_shares.contracts_id')
						->join('cc_base_contracts', 'cc_base_contracts.id', '=', 'cc_channels_contracts.bcontracts_id')
						->where('cc_channels_contracts.channel_id', $authPlatformID)
						->where('cc_base_contracts.films_id', '341')
						->where('cc_contracts_shares.companies_id', $authCompanyID)->select('cc_contracts_shares.*')->get();
				}
				$contractShareInfo = $this->request->film->basecontract;

				return view('titles.titleManagement.rights.partials.change' . $type . '.change' . $type, compact('film', 'cp','geoTemplates','converted', 'countries','authCompanyID', 'contractShareInfo'))->render();
			}

			else if($type === 'Store') {
				$cp = $this->request->film->companies->keyBy('id');
				$type = CHhelper::filterInput($this->request->Input('type'));
				$film = $this->request->film;

				return view('titles.titleManagement.rights.partials.change' . $type . '.change' . $type, compact('film','cp', 'contractShareInfo'))->render();
			}

    }

    /**
     *@POST("/titles/rights/saveRentalInfo")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */	
    public function saveRentalInfo()
    {
		if(!empty($this->request->Input('lease_duration')) && is_numeric($this->request->Input('lease_duration'))){
			$lease_duration = CHhelper::filterInputInt($this->request->Input('lease_duration'));
			
			return Film::where('id', $this->request->filmId)->update([
				'lease_duration' => $lease_duration,
			]);
		}
    }

    /**
     *@POST("/titles/rights/getCP")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function getCP()
    {
        if(!empty($this->request->Input('inputToken')))
            $token = CHhelper::filterInput($this->request->Input('inputToken'));
        $companies = Company::where('deleted', '0')->where('title', 'like', $token.'%')->get()->toArray();
		array_unshift($companies, ['title' => '<b>'.$token.'</b>']);
		return $companies;  
    }

    /**
     *@POST("titles/rights/cpAttach")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function cpAttach()
    {
		if(!empty($this->request->Input('cpid')) && is_numeric($this->request->Input('cpid'))){
			$cpId = CHhelper::filterInputInt($this->request->Input('cpid'));
			
		    $contractsId =  FilmOwners::create([
				'owner_id' => $cpId ,
				'films_id' => $this->request->film->id ,
				'type' => 0 ,
				'role' => 1
		    ])->id;	

			return ContractsShares::create([
				'contracts_id' => $contractsId ,
				'companies_id' => $cpId
			])->id;
			
		}
    }

    /**
     *@POST("titles/rights/deAttachCp")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function deAttachCp()
    {
		if(!empty($this->request->Input('cpid')) && is_numeric($this->request->Input('cpid'))){
			$cpId = CHhelper::filterInputInt($this->request->Input('cpid'));
			
			// $contractsDel = FilmOwners::where('owner_id', $cpId)->where('films_id', $this->request->film->id)->update([
				// 'deleted' => 1
			// ]);
			
		    // $contractsId =  FilmOwners::create([
				// 'owner_id' => $cpId ,
				// 'films_id' => $this->request->film->id ,
				// 'type' => 0 ,
				// 'role' => 1
		    // ])->id;	

			/* return ContractsShares::create([
				'contracts_id' => $contractsId ,
				'companies_id' => $cpId
			])->id;	 */	
		}
    }

	/**
	 *@POST("/titles/rights/drawCountries")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 */
	public function drawCountries()
	{
		if(!empty($this->request->Input('cpid')) && is_numeric($this->request->Input('cpid'))){

			$cpId = CHhelper::filterInputInt($this->request->Input('cpid'));
			$countries = $this->request->film->geoCountries->where('companies_id',$cpId)->keyBy('countries_id');
			return view('titles.titleManagement.rights.partials.editPrice.partials.allCountries',
				compact('countries'))->render();

		}
	}

	
    /**
     *@POST("titles/rights/allRentPrice")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function allRentPrice()
    {	
		if(!empty($this->request->Input('cpid')) && is_numeric($this->request->Input('cpid'))){
			if(!empty($this->request->Input('rentPrice')) && is_numeric($this->request->Input('rentPrice'))){
				$cpId = CHhelper::filterInputInt($this->request->Input('cpid'));
				$rentPrice = CHhelper::filterInputInt($this->request->Input('rentPrice'));
				
				GeoContracts::where('films_id', $this->request->film->id)->update([
					'rent_price_nominal' => $rentPrice
				]);
				
				GeoContracts::where('films_id', $this->request->film->id)->where('rent_price_national', '0')->update([
					'rent_price_nominal' => $rentPrice
				]);
				
				$html = $this->drawCountries();
			
				return [
					'error' => '0' ,
					'message' => 'Start Date And End Date Updated' ,
					'html' => $html 
				];	
			}			
		}
		return [
			'error' => '1' ,
			'message' => 'Error In Update Dates'
		];		
    }

    /**
     *@POST("titles/rights/allBuyPrice")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function allBuyPrice()
    {	
		if(!empty($this->request->Input('cpid')) && is_numeric($this->request->Input('cpid'))){
			if(!empty($this->request->Input('buyPrice')) && is_numeric($this->request->Input('buyPrice'))){
				$buyPrice = CHhelper::filterInputInt($this->request->Input('buyPrice'));
				
				GeoContracts::where('films_id', $this->request->film->id)->update([
					'buy_price_nominal' => $buyPrice
				]);
				
				GeoContracts::where('films_id', $this->request->film->id)->where('buy_price_national', '0')->update([
					'buy_price_nominal' => $buyPrice
				]);
				$html = $this->drawCountries();
			
				return [
					'error' => '0' ,
					'message' => 'Start Date And End Date Updated' ,
					'html' => $html 
				];			
			}		
		}	
		return [
			'error' => '1' ,
			'message' => 'Error In Update Dates'
		];
    }

    /**
     *@POST("titles/rights/allDate")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function allDate()
    {	
		if(!empty($this->request->Input('cpid')) && is_numeric($this->request->Input('cpid'))){
			if(!empty($this->request->Input('startDate')) ||  !empty($this->request->Input('endDate'))){
				$cpId = CHhelper::filterInputInt($this->request->Input('cpid'));
				$startDate = CHhelper::filterInput($this->request->Input('startDate'));
				$endDate = CHhelper::filterInput($this->request->Input('endDate'));
				
				GeoContracts::where('films_id', $this->request->film->id)->where('companies_id', $cpId)->update([
					'start_date' => $startDate ,
					'end_date' => $endDate
				]);
				$html = $this->drawCountries();
			
				return [
					'error' => '0' ,
					'message' => 'Start Date And End Date Updated' ,
					'html' => $html 
				];
			}				
		}
		return [
			'error' => '1' ,
			'message' => 'Error In Update Dates'
		];
    }

    /**
     *@POST("/titles/rights/saveDealsCountriesPL")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */	
    public function saveDealsCountriesPL()	
	{
		if(!empty($this->request->Input('cpid')) && is_numeric($this->request->Input('cpid'))){
			$countries = array();
			$cpId = CHhelper::filterInputInt($this->request->Input('cpid'));
			$targetList = $this->request->Input('targetList');
			if(count($targetList) != 0){
				foreach ($targetList as $key => $values)
				{
					$countries[$values['value']] = array($values['value'], $values['content']);
				}				
			}

			$this->connectDealToCounries($countries, $cpId);		
		}
	}    
	
	/**
     *@POST("/titles/rights/saveContriesPrices")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */	
    public function saveContriesPrices()	
	{
		if(!empty($this->request->Input('item'))){
			foreach($this->request->Input('item') as $key => $val){
				$geoContractId = Chhelper::filterInputInt($key);
				GeoContracts::where('id', $geoContractId)->update([
					'start_date' => Chhelper::filterInput($val['start']) ,
					'end_date' => Chhelper::filterInput($val['end']) ,
					'rent_price_nominal' => Chhelper::filterInput($val['rent_price_nominal']) ,
					'rent_price' => Chhelper::filterInput($val['rent_price']) ,
					'rent_price_national' => Chhelper::filterInput($val['rent_price_national']) ,
					'buy_price_nominal' => Chhelper::filterInput($val['buy_price_nominal']) ,
					'buy_price' => Chhelper::filterInput($val['buy_price']) ,
					'buy_price_national' => Chhelper::filterInput($val['buy_price_national'])
				]);
			}
		}
		
	}

	/**
	 *@POST("/titles/rights/saveCountryItem")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 */
	public function saveCountryItem()
	{
		if(!empty($this->request->Input('item')) && is_array($this->request->Input('item'))){
			foreach($this->request->Input('item') as $key => $val){
				$geoId = Chhelper::filterInputInt($key);
				GeoContracts::where('id', $geoId)->where('deleted', '0')->update([
					'start_date' => Chhelper::filterInput($val['start']) ,
					'end_date' => Chhelper::filterInput($val['end']) ,
					'rent_price_nominal' => Chhelper::filterInput($val['rent_price_nominal']) ,
					'rent_price' => Chhelper::filterInput($val['rent_price']) ,
					'rent_price_national' => Chhelper::filterInput($val['rent_price_national']) ,
					'buy_price_nominal' => Chhelper::filterInput($val['buy_price_nominal']) ,
					'buy_price' => Chhelper::filterInput($val['buy_price']) ,
					'buy_price_national' => Chhelper::filterInput($val['buy_price_national'])
				]);

				return [
					'error' => '0',
					'message' => 'Countries Item Saved Successfuly!'
				];
			}
		}

		return [
			'error' => '1',
			'message' => 'Error In Saved Countries Item!'
		];

	}

	/**
	 *@POST("/titles/rights/saveContractSharePL")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 */
	public function saveContractSharePL()
	{
		$type = !empty($this->request->Input('type'))?$this->request->Input('type'):'';
		if($type == 'Cp'){
			$baseContract = $this->request->film->basecontract;
			$pl_share = $baseContract->share_pl;
			$ch_share = $baseContract->share_ch;

			$new_share_cp = $this->request->Input('share_cp');
			$new_share_pl = 100 - $ch_share - $new_share_cp;

			if($new_share_pl < 0)
			{
				$new_share_pl = 0;
				$new_share_cp = 100 - $ch_share;
			}

			return BaseContracts::where('id', $this->request->film->basecontract->id)->update([
				'share_cp' => $new_share_cp ,
				'share_pl' => $new_share_pl ,
				'share_type' => $this->request->Input('share_type') ,
                'share_fee' => $this->request->Input('share_fee')
			]);
		}


		$shareId = CHhelper::filterInputInt($this->request->Input('share_contract_id'));
		$sharePl = $this->request->Input('share_pl');
		$shareCp = $this->request->Input('share_cp');
		$shareCh = $this->request->Input('share_ch');
		$shareFee = $this->request->Input('share_fee');
		$cpShareType = $this->request->Input('cp_share_type');
		$shareCount = (int)$sharePl + (int)$shareCp + (int)$shareCh;

		if(($shareCount > 100) || ($shareCount < 0)){
			return 0;
		}
		else{
			return ContractsShares::where('id', $shareId)->update([
				'share_type' => $cpShareType ,
			    'share_fee'  => $shareFee ,
			    'share_pl'   => $sharePl ,
			    'share_ch'   => $shareCh ,
			    'share_cp'   => $shareCp
			]);
		}
	}

    private function connectDealToCounries($countries, $cpid){
        $bcontract = $this->request->film->basecontract;
        $alreadyDeletedCountries = array();
        $alreadyExistsCountries = array();
        $countries = empty($countries)?array():$countries;

		//GeoContracts::where('films_id', $this->request->film->id)->where('companies_id', $cpid)->where('bcontracts_id', $bcontract->id)->delete();

		/*foreach ($countries as $key => $value)
		{
			$countriesId = Countries::where('title', $value[1])->select('id')->get()->first()->id;
			GeoContracts::create([
				'bcontracts_id' => $bcontract->id ,
				'films_id' => $this->request->film->id ,
				'companies_id' => $cpid ,
				'countries_id' => $countriesId ,
				'start_date' =>  $bcontract->start_date ,
				'end_date' => $bcontract->end_date ,
				'rent_price_nominal' => $bcontract->rent_price ,
				'buy_price_nominal' => $bcontract->buy_price ,
				'rent_price' => $bcontract->rent_price ,
				'buy_price' => $bcontract->buy_price ,
			]);
		}*/


		$row = GeoContracts::where('bcontracts_id', $bcontract->id)
							->where('companies_id', $cpid)
							->where('films_id', $this->request->film->id)
							->where('deleted', '0')
							->get()->toArray();
		
		if(count($row) != 0){
			foreach($row as $val){
				if($val['deleted']==0)
					$alreadyExistsCountries[$val['id']] = $val;
				elseif($val['deleted'] == 1)
					$alreadyDeletedCountries[$val['id']] = $val;
			}			
		}

        $forDeleteArray = array_diff_key($alreadyExistsCountries, $countries);
		$deleteIds = array_keys($forDeleteArray);

        $newEntries = array_diff_key($countries, $alreadyExistsCountries);

		GeoContracts::where('bcontracts_id', $bcontract->id)->where('companies_id', $cpid)->whereIn('id', $deleteIds)->delete();

        foreach ($newEntries as $key => $value)
        {
			GeoContracts::create([
				'bcontracts_id' => $bcontract->id ,
				'films_id' => $this->request->film->id ,
				'companies_id' => $cpid ,
				'countries_id' => $key ,
				'start_date' =>  $bcontract->start_date ,
				'end_date' => $bcontract->end_date ,
				'rent_price_nominal' => $bcontract->rent_price ,
				'buy_price_nominal' => $bcontract->buy_price ,
				'rent_price' => $bcontract->rent_price ,
				'buy_price' => $bcontract->buy_price ,
			]);
        }
    }

	private function getDiffGeoCountries($CPCountries = [], $FilmCountries = [], $geoCountries = [])
	{
		return array_diff($geoCountries, array_diff($CPCountries, $FilmCountries));
	}

	public function getDiffArray($geoCountries, $FilmCountries)
	{
		$diffArray = array();

		foreach($geoCountries as &$val){
			foreach($FilmCountries as $value){
				if(!empty($val->title)){
					if($val->title == $value->title){
						unset($val->title);
					}
				}
			}
		}

		return $geoCountries;
	}

    /**
     *@POST("/titles/rights/loadCpCounties")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */	
    public function loadCpCounties()
    {
		$film = $this->request->film;
		$cpId = CHhelper::filterInputInt($this->request->Input('cpid'));
		$countries = $this->request->film->geoCountries->where('companies_id',$cpId)->keyBy('countries_id');

        $geoTemplates = GeoTemplates::where('deleted', '0')->get()->keyBy('id');
		$geoCountries = $geoTemplates->first()->id;
		$converted = $this->getNewGeoTemplateData($geoTemplates->first()->id, $countries);

		$user = Auth::user();
		$accountInfo = $user->account;
		$platformsId = $accountInfo->platforms_id;


		$contractShareInfo = DB::table('cc_contracts_shares')
				->join('cc_channels_contracts', 'cc_channels_contracts.id', '=', 'cc_contracts_shares.contracts_id')
				->join('cc_base_contracts', 'cc_base_contracts.id', '=', 'cc_channels_contracts.bcontracts_id')
				->where('cc_channels_contracts.channel_id', $platformsId)
				->where('cc_base_contracts.films_id', '341')
				->where('cc_contracts_shares.companies_id', $cpId)->select('cc_contracts_shares.*')->get();
		if(empty($contractShareInfo)){
			$contractId = '159';
			ContractsShares::create([
				'contracts_id' => $contractId ,
				'companies_id' => $cpId
			]);
			$contractShareInfo = DB::table('cc_contracts_shares')
				->join('cc_channels_contracts', 'cc_channels_contracts.id', '=', 'cc_contracts_shares.contracts_id')
				->join('cc_base_contracts', 'cc_base_contracts.id', '=', 'cc_channels_contracts.bcontracts_id')
				->where('cc_channels_contracts.channel_id', $platformsId)
				->where('cc_base_contracts.films_id', '341')
				->where('cc_contracts_shares.companies_id', $cpId)->select('cc_contracts_shares.*')->get()[0];
		}else{
			$contractShareInfo = $contractShareInfo[0];
		}
		//dd($contractShareInfo);
        return view('titles.titleManagement.rights.partials.changeStore.geoTemplate', compact('film', 'geoTemplates', 'geoCountries', 'countries', 'converted', 'contractShareInfo'))->render();
    }
	
    /**
     *@POST("/titles/rights/loadNewGeoTemplate")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */	
    public function loadNewGeoTemplate()
    {
		$countries = array();
		$geoId = CHhelper::filterInputInt($this->request->Input('geoId'));
		$countries = $this->request->Input('targetList');

		return json_encode($this->getNewGeoTemplateData($geoId, $countries));
	}
	//universal Function support to all types . cp , store


	private function getNewGeoTemplateData($geoId, $countries)
	{

		$converted =  ['remaining'=>'','target'=>''];
		if (gettype($countries) == 'string') {
			$tempCountries = json_decode($countries);
			foreach ($tempCountries as $key => $val) {
				$countriesData[$val->value] = (object)array('title' => $val->content, 'id' => $val->value);
			}


			//$countriesData = (object)$countriesData;
		}
		else{
			$countriesData = $countries;
		}

		$geoTemplates = GeoTemplates::where('deleted', '0')->where('id', $geoId)->get()->keyBy('id');
		if(empty($countriesData))
			$geoCountries = $geoTemplates->first()->countries->keyBy('id');
		else
			$geoCountries = $this->getDiffArray($geoTemplates->first()->countries->keyBy('id'), $countriesData);

		if(!empty($countriesData) && count($countriesData) != 0){
			foreach($geoCountries as $key => $val){
				if(!empty($val->title)){
					$tf = 'true';
					if(!empty($countriesData[$key]))
						$tf = 'false';
					$converted['remaining'][]= array('value'=>$key,'content'=>$val->title, 'status'=>$tf);
				}
			}
			
			foreach($countriesData as $key => $val){
				$converted['target'][]=array('value'=>$val->id, 'content'=>$val->title);
			}
		}else{
			foreach($geoCountries as $key => $val){
				if(!empty($val->title)) {
					$converted['remaining'][] = array('value' => $key, 'content' => $val->title, 'status' => true);
				}
			}
		}
		return $converted;
	}

	
}

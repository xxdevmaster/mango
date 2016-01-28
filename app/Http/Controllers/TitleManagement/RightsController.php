<?php
namespace App\Http\Controllers\TitleManagement;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Libraries\CHhelper\CHhelper;
use App\Film;
use App\Company;
use DB;
use App\Models\GeoTemplates;

class RightsController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function rightsShow()
    {
        $current_menu = '';
        $film = $this->request->film;
		
        return view('titles.titleManagement.rights.rights', compact('current_menu', 'film'));
    }

    /**
     *@POST("/titles/rights/getChangeCPPL")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function getChangeCPPL()
    {
        if(!empty($this->request->Input('type')) && $this->request->Input('type') === 'CP' || $this->request->Input('type') === 'Store'){
            $type = CHhelper::filterInput($this->request->Input('type'));
			$film = $this->request->film;
			
            return view('titles.titleManagement.rights.partials.change'.$type, compact('film'))->render();
        }else
			return [
                'error' => '1',
                'message' => 'Content Provider or Store dont defined'
            ];
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
       // dd($this->request->all());
        if(!empty($this->request->Input('inputToken')))
            $token = CHhelper::filterInput($this->request->Input('inputToken'));
        $companies = Company::where('deleted', '0')->where('title', 'like', $token.'%')->get()->toArray();
		array_unshift($companies, ['title' => '<b>'.$token.'</b>']);
		return $companies;  
    }
	
    /**
     *@POST("/titles/rights/loadCpCounties")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */	
    public function loadCpCounties()
    {
        $this->request->film->geoCountries;
        $geoTemplates = GeoTemplates::where('deleted', '0')->get()->keyBy('id');

        $geoCountries = $geoTemplates->first()->countries;		
		dd($geoCountries);
        return view('titles.titleManagement.rights.partials.partials.actionCP', compact('geoTemplates', 'geoCountries'))->render();
    }
	

    // private function getContractCountries($cpid,$film_id){
        // $q = "SELECT cc_geo_contracts.*,cc_countries.id as cid, cc_countries.title as ctitle,cc_countries.currency_code as ccode, DATE_FORMAT(cc_geo_contracts.start_date,'%Y-%m-%d') as format_start_date,DATE_FORMAT(cc_geo_contracts.end_date,'%Y-%m-%d') as format_end_date 
            // FROM cc_geo_contracts LEFT JOIN cc_countries ON cc_geo_contracts.countries_id=cc_countries.id 
            // WHERE cc_geo_contracts.films_id='{$film_id}' 
                // AND cc_geo_contracts.deleted=0
                // AND cc_geo_contracts.companies_id='".$cpid."';";
            
        // $res= G('DB')->query($q);
        // while($row = $res->fetch(PDO::FETCH_ASSOC))
        // {
            // $countries[$row['cid']] = $row;
        // }
        // return $countries;
    // }

	
	// private function getGeoCountries($geoId, $filmId)
	// {

		// $geoCountries = DB::table('fk_geotemplates_countries')
				// ->join('cc_countries', 'cc_countries.id', '=', 'fk_geotemplates_countries.countries_id')
				// ->where('fk_geotemplates_countries.geotemplates_id', $geoId)
				// ->whereNotIn('cc_countries', DB::select(DB::raw("SELECT cc_geo_contracts.countries_id FROM cc_geo_contracts WHERE cc_geo_contracts.deleted=0  AND cc_geo_contracts.films_id=".$filmId)))
				// ->get();
		//dd($geoCountries);
        // foreach($geoCountries as $key => $val){
            // $tf = 'true';
            // if(!empty($countries[$key]))
                    // $tf = 'false';
            // $converted['remaining'].='{value:'.$val[0].', content:"'.$val[1].'", status:'.$tf.'},';
        // }
        // foreach($countries as $key => $val){
            // $converted['target'].='{value:'.$val['cid'].', content:"'.$val['ctitle'].'"},';
        // }
        // return $converted;	
				
		//dd($x);
		
        // $q="
// SELECT fk_geotemplates_countries.*,cc_countries.id as cid, cc_countries.title as ctitle  
            // FROM fk_geotemplates_countries 
            // LEFT JOIN cc_countries ON fk_geotemplates_countries.countries_id=cc_countries.id 
            // WHERE fk_geotemplates_countries.geotemplates_id='{$geoId}' AND cc_countries.id  NOT IN (SELECT cc_geo_contracts.countries_id FROM cc_geo_contracts WHERE cc_geo_contracts.deleted=0  AND cc_geo_contracts.films_id='".$film_id."')            
// ";
		//dd(DB::selectRaw($q));
        // while($row = $res->fetch(PDO::FETCH_ASSOC))
        // {
           // $geoCountries[$row['cid']] = array($row['cid'],$row['ctitle']);
        // }
        // return $geoCountries;
    //}
	
}

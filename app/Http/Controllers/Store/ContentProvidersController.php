<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Libraries\CHhelper\CHhelper;
use Auth;
use App\Film;
use App\Company;
use App\Account;
use Aws\Common\Aws;
use DB;
class ContentProvidersController extends Controller
{
    private $request;

    private $authUser;

    private $accountID;

    private $storeID;

    private $companyID;

    private $limit = 20;

    private $offset = 0;

    private $page = 0;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->authUser = Auth::user();
        $this->accountID = $this->authUser->account->id;
        $this->storeID = $this->authUser->account->platforms_id;
        $this->companyID = $this->authUser->account->companies_id;
    }

    public function contentProvidersShow()
    {
        $contentProviders = $this->getContentProviders();
        return view('store.contentProviders.contentProviders', compact('contentProviders'));
    }

    private function getContentProviders()
    {
        $films = $this->getAllFilms();
        $searchWord = !empty($this->request->Input('searchWord')) ? CHhelper::filterInput($this->request->Input('searchWord')) : '' ;


        // cinehost
        if($this->companyID == 1){
            $total = Company::where('deleted', '0')->where('title', 'like', $searchWord.'%')->count();

            $items = Company::where('deleted', '0')
                            ->where('title', 'like', $searchWord.'%')
                            ->select('cc_companies.id', 'cc_companies.title', 'cc_companies.logo', 'cc_companies.website')
                            ->orderBy('title', 'asc')
                            ->limit($this->limit)->offset($this->offset)->get();
        }
        else{
            $q = "SELECT COUNT(*) as total FROM (
                SELECT cc_companies.id
                FROM cc_companies
                LEFT JOIN fk_films_owners ON cc_companies.id=fk_films_owners.owner_id
                Left JOIN cc_accounts ON cc_accounts.companies_id= cc_companies.id
                WHERE (cc_accounts.parent_id = $this->accountID OR (fk_films_owners.films_id IN (".implode(',',$films).") AND fk_films_owners.type=0)) AND cc_companies.title <> ''
                AND cc_companies.title LIKE '".$searchWord."%'  GROUP BY cc_companies.id) as total";

            $total = DB::select($q)[0]->total;

            $items = Company::leftJoin('fk_films_owners', 'cc_companies.id', '=', 'fk_films_owners.owner_id')
                            ->leftJoin('cc_accounts', 'cc_accounts.companies_id', '=', 'cc_companies.id')
                            ->where(function($query){
                                $query->where('cc_accounts.parent_id', $this->accountID)
                                    ->orWhere(function($q){
                                        $films = $this->getAllFilms();
                                        $q->whereIn('fk_films_owners.films_id', $films)
                                            ->where('fk_films_owners.type', 0);
                                    });
                            })
                            ->where('cc_companies.title', '<>', '')
                            ->where('cc_companies.title', 'like', $searchWord.'%')
                            ->select('cc_companies.id', 'cc_companies.title', 'cc_companies.logo', 'cc_companies.website')
                            ->groupBy('cc_companies.id')->limit($this->limit)->offset($this->offset)->get()->keyBy('id');

            $items = $items->each(function($item, $key){
                $filmsIDS = $this->getAllFilms();
                $item->setAttribute('titlesCount', $this->getContentProviderFilmsCount($key, $filmsIDS)->first()->titlesCount);
            });
        }
        return new lengthawarepaginator($items, $total, $this->limit, $this->page);
    }

    private function getAllFilms()
    {
        if($this->companyID == 1) // cinehost
            return Film::where('deleted', '0')->select('id')->lists('id')->toArray();
        else
            return Film::join('fk_films_owners', 'fk_films_owners.films_id', '=', 'cc_films.id')
                        ->where('fk_films_owners.owner_id', $this->storeID)->where('fk_films_owners.type', '1')
                        ->where('cc_films.deleted', '0')->select('cc_films.id')->lists('cc_films.id')->toArray();
    }

    private function getContentProviderFilmsCount($providerID, $filmsIDS)
    {
        if($this->companyID == 1)
            return Film::join('fk_films_owners', 'cc_films.id', '=', 'fk_films_owners.films_id')
                  ->where('cc_films.deleted', 0)
                  ->where('fk_films_owners.owner_id', $providerID)
                  ->where('fk_films_owners.type', 0)->select(DB::raw('COUNT(cc_films.id) as titlesCount'))->get();
        else
            return \App\FilmOwners::where('owner_id', $providerID)
                                    ->where('type', 0)
                                    ->whereIn('films_id', $filmsIDS)
                                    ->select(DB::raw('COUNT(*) as titlesCount'))->get();
    }

    private function getContentProviderFilms($providerID, $filmsIDS)
    {
        if($this->companyID == 1)
            return Film::join('fk_films_owners', 'cc_films.id', '=', 'fk_films_owners.films_id')
                        ->where('cc_films.deleted', 0)
                        ->where('fk_films_owners.owner_id', $providerID)
                        ->where('fk_films_owners.type', 0)
                        ->select('cc_films.id', 'cc_films.title', 'cc_films.cover')->limit($this->limit)->offset($this->offset)->get()->keyBy('id');
        else
            return Film::join('fk_films_owners', 'cc_films.id', '=', 'fk_films_owners.films_id')
                        ->where('cc_films.deleted', 0)
                        ->where('fk_films_owners.owner_id', $providerID)
                        ->where('fk_films_owners.type', 0)
                        ->whereIn('films_id', $filmsIDS)->select('cc_films.id', 'cc_films.title', 'cc_films.cover')
                        ->limit($this->limit)->offset($this->offset)->get()->keyBy('id');
    }

    /**
     *@POST("/store/contentProviders/filterSearch")
     * @Middleware("auth")
     */
    public function filterSearch()
    {
        $contentProviders = $this->getContentProviders();
        return view('store.contentProviders.list_partial', compact('contentProviders'))->render();
    }

    /**
     *@POST("/store/contentProviders/pager")
     * @Middleware("auth")
     */
    public function pager()
    {
        if(!empty($this->request->Input('page')) && is_numeric($this->request->Input('page'))){
            $this->page = CHhelper::filterInputInt($this->request->Input('page'));
            if(($this->page - 1) != 0)
                $this->offset  = ($this->page - 1)*20;
            $contentProviders = $this->getContentProviders();
            return view('store.contentProviders.list_partial', compact('contentProviders'))->render();
        }
    }

    /**
     *@POST("/store/contentProviders/createNewContentProvider")
     * @Middleware("auth")
     */
    public function createNewContentProvider(){
        $cpName = !empty($this->request->Input('contentProviderName')) ? CHhelper::filterInput($this->request->Input('contentProviderName')) : false;
        if($cpName){
            $newCpID = Company::create(['title' => $cpName])->id;

            Account::create([
                'title' => $cpName ,
                'status' => 'free' ,
                'companies_id' => $newCpID,
                'level' => '-1' ,
                'source' => 'admin_panel' ,
                'parent_id' => $this->accountID,
            ]);

        }

        $contentProviders = $this->getContentProviders();
        return view('store.contentProviders.list_partial', compact('contentProviders'))->render();
    }

    /**
     *@POST("/store/contentProviders/getContentProviderInfo")
     * @Middleware("auth")
     */
    public function getContentProviderInfo()
    {
        $contentProviderID = (!empty($this->request->Input('contentProviderID')) && is_numeric($this->request->Input('contentProviderID'))) ? CHhelper::filterInputInt($this->request->Input('contentProviderID')) : false;

        if($contentProviderID) {
            $company = Company::find($contentProviderID);
            $account = Account::where('companies_id', $contentProviderID)->get();

            if(!$account->isEmpty()) {
                if($account->first()->parent_id === $this->accountID) {

                }
            }

            return view('store.contentProviders.editForm', compact('company'))->render();
        }
    }

    /**
     *@POST("/store/contentProviders/editContentProviderInfo")
     * @Middleware("auth")
     */
    public function editContentProviderInfo()
    {
        $companyID = (!empty($this->request->Input('contentProviderID')) && is_numeric($this->request->Input('contentProviderID'))) ? CHhelper::filterInputInt($this->request->Input('contentProviderID')) : false;
        if($companyID){
            Company::where('id', $companyID)->update([
                'title' => CHhelper::filterInput($this->request->Input('title')) ,
                'logo' => CHhelper::filterInput($this->request->Input('logo')) ,
                'brief' => CHhelper::filterInput($this->request->Input('brief')) ,
                'website' => CHhelper::filterInput($this->request->Input('website')) ,
            ]);
        }

        $contentProviders = $this->getContentProviders();
        return view('store.contentProviders.list_partial', compact('contentProviders'))->render();
    }

    /**
     *@POST("/store/contentProviders/uploadLogo")
     * @Middleware("auth")
     */
    public function uploadLogo()
    {
        $s3AccessKey = 'AKIAJPIY5AB3KDVIDPOQ';
        $s3SecretKey = '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';
        $region    	 = 'us-east-1';
        $bucket		 = 'cinecliq.assets';

        $fileTypes = array('jpg','jpeg','png','PNG','JPG','JPEG','svg','SVG'); // File extensions

        $s3path = $this->request->file('Filedata');
        $s3name = $s3path->getClientOriginalName();
        $s3mimeType = $s3path->getClientOriginalExtension();


        list($_width, $_height) = @getimagesize($this->request->file('Filedata'));

        if(in_array($s3mimeType, $fileTypes)){
            if ($_width > 350 || $_height > 350){
                return [
                    'error' => '1',
                    'message' => 'Unable to upload. Please make sure your image is 350x350px.'
                ];
            }
            $s3 = AWS::factory([
                'key'    => $s3AccessKey,
                'secret' => $s3SecretKey,
                'region' => $region,
            ])->get('s3');

            $s3->putObject([
                'Bucket' => $bucket,
                'Key'    => 'files/'.$s3name,
                'Body'   => fopen($s3path, 'r'),
                'SourceFile' => $s3path,
                'ACL'    => 'public-read',
            ]);

            return [
                'error' => 0,
                'message' => $s3name
            ];
        }else
            $response = [
                'error' => 1,
                'message' => $s3mimeType.' is invalid file type'
            ];

        return $response;
    }

    /**
     *@GET("/store/contentProviders/films/{contentProviderID}")
     * @Middleware("auth")
     * @Where({"contentProviderID": "[0-9]+"})
     */
    public function contentProvidersFilmsShow($contentProviderID)
    {
        $params = $this->getContentProvidersFilms($contentProviderID);
        return view('store/contentProviders.contentProviderFilms', $params);
    }

    private function getContentProvidersFilms($contentProviderID)
    {
        $contentProvider = Company::where('id', $contentProviderID)->get()->first();
        $filmsIDS = $this->getAllFilms();
        $items = $this->getContentProviderFilms($contentProviderID, $filmsIDS);
        $total = $this->getContentProviderFilmsCount($contentProviderID, $filmsIDS)->first()->titlesCount;
        $contentProviderFilms = new lengthawarepaginator($items, $total, $this->limit, $this->page);

        return compact('contentProvider', 'contentProviderFilms');
    }

    /**
     *@POST("/store/contentProviders/films/pager")
     * @Middleware("auth")
     */
    public function contentProviderFilmsPager()
    {
        $contentProviderID = !empty($this->request->Input('contentProviderID')) && is_numeric($this->request->Input('contentProviderID')) ? CHhelper::filterInputInt($this->request->Input('contentProviderID')) : false;
        $this->page = !empty($this->request->Input('page')) && is_numeric($this->request->Input('page')) ? CHhelper::filterInputInt($this->request->Input('page')) : false;
        if($contentProviderID && $this->page){
            if(($this->page - 1) != 0)
                $this->offset  = ($this->page - 1)*20;

            $params = $this->getContentProvidersFilms($contentProviderID);
            return view('store.contentProviders.filmList_partial', $params);
        }
    }
}

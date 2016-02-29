<?php

namespace App\Http\Controllers\Xchange;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\Libraries\CHhelper\CHhelper;
use App\Company;
use Illuminate\Pagination\LengthAwarePaginator;

class XchangeContentProvidersController extends Controller
{
    private $request;

    private $authUser;

    private $limit = 20;

    private $offset = 0;

    private $page = 0;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->authUser = Auth::user();
    }

    public function contentProvidersShow()
    {
        $companies = $this->getCompanies();
        return view('xchange.xchangeContentProviders.xchangeContentProviders', compact('companies'));
    }

    private function getCompanies()
    {
        $condition = !empty($this->request->Input('searchWord')) ? " AND (cc_companies.title LIKE '%".CHhelper::filterInput($this->request->Input('searchWord'))."%')" : '';

        $companiesTotalCount = Company::getXchangeContentProvidersCountAll($condition)->first()->count;
        $companies = Company::getXchangeContentProviders($condition, $this->limit, $this->offset)->keyBy('id');

        $companies->each(function ($item) {
            $item->setAttribute('filmsCount', $item->companyVaultsFilmsCountAll->first()->count);
        });

        return new lengthawarepaginator($companies, $companiesTotalCount, $this->limit, $this->page);
    }

    /**
     *@POST("/xchange/contentProviders/filterSearch")
     * @Middleware("auth")
     */
    public function filterSearch()
    {
        $companies = $this->getCompanies();
        return view('xchange.xchangeContentproviders.list_partial', compact('companies'))->render();
    }

    /**
     *@GET("/xchange/contentProviders/films/{companyID}")
     * @Where({"id": "[0-9]+"})
     * @Middleware("auth")
     */
    public function contentProviderFilms($companyID)
    {
        $companyID = CHhelper::filterInputInt($companyID);
        $contentProviderFilms = $this->getContentProviderFilms($companyID);
        if($contentProviderFilms === 0)
            return view('errors.550');
        return view('xchange.xchangeContentproviders.contentprovidersFilms', $contentProviderFilms);
    }

    private function getContentProviderFilms($companyID)
    {
        $company = Company::where('id', $companyID)->get();
        if($company->isEmpty())
            return 0;
        else{
            $films = $company->first()->companyVaultsFilms;
            if($films->isEmpty())
                return 0;
        }
        $companyFilmsTotal = $this->getContentProviderFilmCountInVault($companyID);
        $companyFilms = new lengthawarepaginator($films, $companyFilmsTotal, $this->limit, $this->page);
        return compact('company', 'companyFilms');
    }

    /**
     *@POST("/xchange/contentProviders/films/pager")
     * @Middleware("auth")
     */
    public function partnerStoresFilmsPager()
    {
        $companyID = !empty($this->request->Input('companyID')) && is_numeric($this->request->Input('companyID')) ? CHhelper::filterInputInt($this->request->Input('companyID')) : false;
        $this->page = !empty($this->request->Input('page')) && is_numeric($this->request->Input('page')) ? CHhelper::filterInputInt($this->request->Input('page')) : false;
        if($storeID && $this->page){
            if(($this->page - 1) != 0)
                $this->offset  = ($this->page - 1)*20;

            $contentProviderFilms = $this->getContentProviderFilms($companyID);
            return view('xchange.xchangeContentproviders.filmList_partial', $contentProviderFilms);
        }
    }

    private function getContentProviderFilmCount($companyID)
    {
        $q = "SELECT COUNT(cc_films.id) FROM cc_films
            INNER JOIN fk_films_owners ON cc_films.id = fk_films_owners.films_id
            WHERE cc_films.deleted = 0 AND fk_films_owners.owner_id='".$companyID."'  AND type=0";
        $resCnt = G('DB')->query($q)->fetchColumn();
        return $resCnt;
    }

    private function getContentProviderFilmCountInVault($companyID)
    {
        $company = Company::where('id', $companyID)->get();
        return $company->first()->companyVaultsFilmsCountAll->first()->count;
    }
}

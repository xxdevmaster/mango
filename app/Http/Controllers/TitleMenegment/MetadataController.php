<?php

namespace App\Http\Controllers\TitleMenegment;

use Auth;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\LocaleFilms;
use App\Film;
use App\AllLocales;
use App\Genres;
use App\Languages;
use App\ProdCompanies;
use App\Countries;
use App\Models\FilmsGenres;
use App\Models\FilmsLanguages;
use App\Models\FilmsProdCompanies;
use App\Models\FilmsCountries;
use App\Models\Persons;
use App\Models\FilmsPersons;
use App\Models\Jobs;
use App\Models\LocalePersons;
use App\Models\AgeRates;
use App\Models\FilmsAgeRates;
use App\Models\FilmSubtitles;
use App\Models\TrailerSubtitles;
use App\Models\ChannelsFilmsKeywords;
use Illuminate\Routing\Route;
use Illuminate\Support\Debug\Dumper;

use DB;
use Aws\Common\Aws;
use Intervention\Image\Facades\Image;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Http\Annotations\AnnotationsServiceProvider;

use App\Libraries\CHhelper\CHhelper;

class MetadataController extends Controller
{
	private $id;

    public function metadataShow($id)
    {
		$current_menu = 'Metadata';
		$film = $this->getFilm($id);
		if(count($film) === 0) 
			return view('errors.404', compact('current_menu'));		
		$allLocales = $this->getAllLocale();
		
		$metadata = [
			'basic' => $this->getBasic($id) ,
			'advanced' => $this->getAdvanced($id) ,
			'castAndCrew' => $this->getCastAndCrew($id) ,
			'images' => $this->getImages($id) ,
			'subtitles' => $this->getSubtitles($id) ,
			'ageRates' => $this->getAgeRates($id) ,
			'series' => $this->getSeries($id) ,
			'seo' => $this->getSeo($id)
		];
		
		return view('titles.titleMenegment.metadata.metadata', compact('current_menu', 'id', 'film', 'allLocales', 'metadata'));
    }
	
	private function getFilm($id)
	{
		$this->id = (int) $id;     
		
        $userInfo = Auth::user();

        $accountInfo = $userInfo->account;

        $accountFeatures = $accountInfo->features;

        $companyInfo = $accountInfo->company;

        $companyFilms = $companyInfo->films()->where('cc_films.deleted', '0')->get();
		
		$film = $companyInfo->films()->where( 'cc_films.id', $this->id)->get();
		
		if(count($film) != 0) {
			return $film[0];
		}else {
			$storeInfo = $accountInfo->store;
			$storeFilms = $storeInfo->contracts()->with( 'films', 'stores' )->where( 'films_id', $this->id )->get();
			foreach($storeFilms as $storeFilm){
				$film = $storeFilm->films;
			}
		}		

		return $film;
		
	}

	private function getAllLocale()
	{
		$allLocale = AllLocales::select('title', 'code')->get()->toArray();
		
		if(is_array($allLocale) && count($allLocale) > 0){
			foreach($allLocale as $val) {
				$allLocales[$val['code']] = $val['title'];
			}
		}
		return $allLocales;
	}
	
	/**
	 *@POST("/titles/metadata/basic/getTemplate")
	 * @Middleware("auth")
	*/
	public function getTemplate(Request $request)
	{	
		if(empty($request->Input('filmId')) || empty($request->Input('template'))){
			return [
				'error' => '1' ,
				'message' => 'Film Identifier or template doesnt exixst'
			];			
		}
		if(!is_numeric($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Identifier film not valid format'
			];			
		}
		$filmId = CHhelper::filterInputInt($request->Input('filmId'));	
	
		$templateName = CHhelper::filterInput(($request->Input('template')));
		
		if(count($this->getFilm($filmId)) === 0) {
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}
		$template= '';
		$film = $this->getFilm($filmId);
		$allLocales = $this->getAllLocale();	
		
		switch($templateName){
			case 'basic' : $template = $this->getBasic($filmId); break;
			case 'castAndCrew' : $template = $this->getCastAndCrew($filmId); break;
			case 'images' : $template = $this->getImages($filmId); break;
			case 'subtitles' : $template = $this->getSubtitles($filmId); break;
			case 'seo' : $template = $this->getSeo($filmId); break;
		}
		
		$metadata = [
			$templateName => $template
		];
		
		return view('titles.titleMenegment.metadata.partials.'.$templateName.'.'.$templateName, compact('film', 'allLocales', 'metadata'));
	}
	
    private function getBasic($id)
    {			
		$filmLocales = LocaleFilms::where('films_id', $id)->where('deleted', 0)->orderBy('def', 'desc')->orderBy('id', 'asc')->get();	
		$allLocales = $this->getAllLocale();
		$allUniqueLocales = CHhelper::getUniqueLocale($allLocales, $filmLocales);
		
		return compact('filmLocales', 'allUniqueLocales');
		
    } 
	
	/**
	 *@POST("titles/metadata/basicSaveChanges")
	 * @Middleware("auth")
	*/
    public function basicSaveChanges(Request $request)
    {
		if(empty($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Film Identifier doesnt exixst'
			];			
		}
		if(!is_numeric($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Identifier film not valid format'
			];			
		}
		$filmId = CHhelper::filterInputInt($request->Input('filmId'));
		
		if(count($this->getFilm($filmId)) != 0) {
			foreach($request->Input('filmsLocales') as $key => $value) {
				if(array_key_exists($key, $this->getAllLocale())) {
					if(!empty($value['localeId']) && is_numeric($value['localeId'])) {
						$localeId = CHhelper::filterInputInt($value['localeId']);
						
						if(!empty($value['title']))
							$title = CHhelper::filterInput($value['title']);
						else 
							$title = '';
						if(!empty($value['synopsis']))
							$synopsis = CHhelper::filterInput($value['synopsis']);
						else
							$synopsis = '';
						
						$localeUpdate =  LocaleFilms::where('id', $localeId)->where('films_id', $filmId)->update(array(
							'title' => $title,
							'synopsis' => $synopsis,
						));	
						
						if($value['def'] == '1'){
							
							$filmLocaleUpdate =  Film::where('id', $filmId)->update(array(
								'title' => $title,
								'synopsis' => $synopsis,
							));	
							
						}
						
					}					
					else continue;				
				}
			}
			return [
				'error' => '0',
				'message' => 'success'
			];			
		}
		return [
			'error' => '1' ,
			'message' => 'You dont have permission to change'
		];
    } 

	/**
	 *@POST("titles/metadata/basic/newLocale")
	 * @Middleware("auth")
	*/
	public function basicAddNewLocale(Request $request)
    {
		if(empty($request->Input('filmId')) || empty($request->Input('locale'))){
			return [
				'error' => '1' ,
				'message' => 'Film Identifier or film locale doesnt exixst'
			];			
		}
		if(!is_numeric($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film Identifier'
			];			
		}
		
		if(!array_key_exists($request->Input('locale'), $this->getAllLocale())){
			return [
				'error' => '1' ,
				'message' => 'Invalid locale'
			];			
		}
		
		$filmId = CHhelper::filterInputInt($request->Input('filmId'));
		$locale = CHhelper::filterInput($request->Input('locale'));
		
		if(count($this->getFilm($filmId)) != 0){
			$newLocaleId = LocaleFilms::create([
				'films_id' => $filmId,
				'locale' => $locale,
			])->id;	
			if($newLocaleId > 0)	
				return [
					'error' => '0' ,
					'message' => 'Basic locale are inserted',
					'insertedId' => $newLocaleId
				];
			else
				return [
					'error' => '1' ,
					'message' => 'Mysql Server error'
				];								
		}
		
		return [
			'error' => '1' ,
			'message' => 'You dont have perrmisions'
		];
		
    }
	
	/**
	 *@POST("titles/metadata/basic/localeRemove")
	 * @Middleware("auth")
	*/
    public function basicLocaleRemove(Request $request)
    {
		if(empty($request->Input('filmId')) || empty($request->Input('localeId'))){
			return [
				'error' => '1' ,
				'message' => 'Film Identifier or film locale doesnt exixst'
			];			
		}
		if(!is_numeric($request->Input('filmId')) || !is_numeric($request->Input('localeId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film  or Locale Identifier'
			];			
		}
		
		if(count($this->getFilm($request->Input('filmId'))) === 0){
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}		
		
		$filmId = CHhelper::filterInputInt($request->Input('filmId'));
		$localeId = CHhelper::filterInput($request->Input('localeId'));

		$localeRemove =  LocaleFilms::where('id', $localeId)->where('films_id', $filmId)->where('def', '<>', '1')->update(array(
			'deleted' => 1
		));	
		
        if($localeRemove > 0)
			return [
				'error' => '0' ,
				'message' => 'Language is Deleted'
			];
		else
			return [
				'error' => '1' ,
				'message' => 'Mysql Server Error'
			];	
    }   

	/**
	 *@POST("titles/metadata/basic/makeDefaultLocale")
	 * @Middleware("auth")
	*/
	public function makeDefaultLocale(Request $request)
    {
		if(empty($request->Input('filmId')) || empty($request->Input('localeId')) || empty($request->Input('locale'))){
			return [
				'error' => '1' ,
				'message' => 'Film Identifier or film locale doesnt exixst'
			];			
		}
		if(!is_numeric($request->Input('filmId')) || !is_numeric($request->Input('localeId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film  or Locale Identifier'
			];			
		}
		
		if(count($this->getFilm($request->Input('filmId'))) === 0){
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}
		
		if(!array_key_exists($request->Input('locale'), $this->getAllLocale())){
			return [
				'error' => '1' ,
				'message' => 'Invalid locale'
			];			
		}		
		
		$filmId = CHhelper::filterInputInt($request->Input('filmId'));
		$localeId = CHhelper::filterInput($request->Input('localeId'));		
		$locale = CHhelper::filterInput($request->Input('locale'));
		
		$editDefaultLocale =  LocaleFilms::where('films_id', $filmId)->where('def', '1')->update(array(
			'def' => 0
		));
		
		$localeMakeDefault =  LocaleFilms::where('films_id', $filmId)->where('id', $localeId)->update(array(
			'def' => 1
		));	
		
		if($localeMakeDefault > 0){
			$localeMakeDefault =  Film::where('id', $filmId)->update(array(
				'i18n' => $locale,
				'locale' => $locale
			));	
			
			if($localeMakeDefault > 0){
				return [
					'error' => '0' ,
					'message' => 'Default language is maked'
				];				
			}else
				return [
					'error' => '1' ,
					'message' => 'Mysql Server Error'
				];					
		}else
			return [
				'error' => '1' ,
				'message' => 'Mysql Server Error'
			];			
    }	
	
	public function getAdvanced($id)	
	{
		$film = $this->getFilm($id);
		
		if(count($film) != 0){
			$filmGenres = $film->genres()->get()->keyBy('id');
			$filmLanguages = $film->languages()->get()->keyBy('id');
			$filmProdCompanies = $film->prodCompanies()->get()->keyBy('id');
			$filmCountries = $film->countries()->get()->keyBy('id');

			return compact('filmGenres', 'filmLanguages', 'filmProdCompanies', 'filmCountries');			
		}
	}

	/**
	 *@POST("titles/metadata/advanced/getTokenGenres")
	 * @Middleware("auth")
	*/	
	public function getTokenGenres(Request $request)
	{
		if(empty($request->Input('inputToken'))){
			return [
				'error' => '1' ,
				'message' => 'InputToken doesnt exixst'
			];			
		}
		
		$inputToken = CHhelper::filterInput($request->Input('inputToken'));		
		return Genres::where('deleted', '0')->where('title', 'like', $inputToken.'%')->get();		
	}	

	/**
	 *@POST("titles/metadata/advanced/getTokenOriginalLanguages")
	 * @Middleware("auth")
	*/	
	public function getTokenOriginalLanguages(Request $request)
	{
		if(empty($request->Input('inputToken'))){
			return [
				'error' => '1' ,
				'message' => 'InputToken doesnt exixst'
			];			
		}
		
		$inputToken = CHhelper::filterInput($request->Input('inputToken'));
		return Languages::where('deleted', '0')->where('title', 'like', $inputToken.'%')->get(); 
	}	
	
	/**
	 *@POST("titles/metadata/advanced/getTokenProdCompanies")
	 * @Middleware("auth")
	*/	
	public function getTokenProdCompanies(Request $request)
	{
		if(empty($request->Input('inputToken'))){
			return [
				'error' => '1' ,
				'message' => 'InputToken doesnt exixst'
			];			
		}
		
		$inputToken = CHhelper::filterInput($request->Input('inputToken'));		
		return ProdCompanies::where('deleted', '0')->where('title', 'like', $inputToken.'%')->get();		 
	}	
	
	/**
	 *@POST("titles/metadata/advanced/getTokenCountries")
	 * @Middleware("auth")
	*/	
	public function getTokenCountries(Request $request)
	{
		if(empty($request->Input('inputToken'))){
			return [
				'error' => '1' ,
				'message' => 'InputToken doesnt exixst'
			];			
		}
		
		$inputToken = CHhelper::filterInput($request->Input('inputToken'));
		return Countries::where('deleted', '0')->where('title', 'like', $inputToken.'%')->get();
	}

	/**
	 *@POST("titles/metadata/advancedSaveChanges")
	 * @Middleware("auth")
	*/	
	public function advancedSaveChanges(Request $request)
	{
		if(empty($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Film params doesnt exixst'
			];			
		}
		
		if(!is_numeric($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film  or Locale Identifier'
			];			
		}
		
		if(count($this->getFilm($request->Input('filmId'))) === 0){
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}
		
		try{		
			$filmId = CHhelper::filterInputInt($request->Input('filmId'));
			$dt = CHhelper::filterInput($request->Input('filmId'));
			$duration = CHhelper::filterInput($request->Input('filmId'));
			$admComment = CHhelper::filterInput($request->Input('filmId'));

			FilmsGenres::destroy(['films_id', $filmId]);
			if(is_array($request->Input('genres'))){
				foreach($request->Input('genres') as $genresId => $val) {
					if(!is_numeric($genresId)){
						return [
							'error' => '1' ,
							'message' => 'Invalid genres Identifier'
						];					
					}
					$genresId = CHhelper::filterInputInt($genresId);
					
					$fkFilmsGenres = FilmsGenres::create([
						'films_id' => $filmId,
						'genres_id' => $genresId,
					]);
				}				
			}			

			FilmsLanguages::destroy(['films_id', $filmId]);
			if(is_array($request->Input('originalLanguages'))){
				foreach($request->Input('originalLanguages') as $originalLanguagesId => $val) {
					if(!is_numeric($originalLanguagesId)){
						return [
							'error' => '1' ,
							'message' => 'Invalid language Identifier'
						];					
					}
					$genresId = CHhelper::filterInputInt($originalLanguagesId);
					
					$fkFilmsLanguages = FilmsLanguages::create([
						'films_id' => $filmId,
						'languages_id' => $originalLanguagesId,
					]);
				}		
			}		
			
			FilmsProdCompanies::destroy(['films_id', $filmId]);
			if(is_array($request->Input('productCompanies'))){
				foreach($request->Input('productCompanies') as $productCompaniesId => $val) {
					if(!is_numeric($productCompaniesId)){
						return [
							'error' => '1' ,
							'message' => 'Invalid production company Identifier'
						];					
					}
					$genresId = CHhelper::filterInputInt($productCompaniesId);
					
					$fkFilmsProductCompanies = FilmsProdCompanies::create([
						'films_id' => $filmId,
						'prodcompanies_id' => $productCompaniesId,
					]);
				}		
			}		
			
			FilmsCountries::destroy(['films_id', $filmId]);
			if(is_array($request->Input('productCompanies'))){
				foreach($request->Input('countries') as $countriesId => $val) {
					if(!is_numeric($countriesId)){
						return [
							'error' => '1' ,
							'message' => 'Invalid countries company Identifier'
						];					
					}
					$genresId = CHhelper::filterInputInt($countriesId);
					
					$fkFilmsProductCountries = FilmsCountries::create([
						'films_id' => $filmId,
						'countries_id' => $countriesId,
					]);
				}
			}
			
			$fkFilmsProductCountries = Film::where('id', $filmId)->update([
				'id' => $filmId,
				'dt' => $dt,
				'duration' => $duration,
				'admcomment' => $admComment,
			]);	

			return [
				'error' => '0' ,
				'message' => 'Saved'
			];
				
		}catch (Exception $e){
			return $e->getMessage();
		}		
	}

	private function getCastAndCrew($id)	
	{
		$film = $this->getFilm($id);
		$person = $film->persons()->get();
		
		return compact('person');
	}

	/**
	 *@POST("titles/metadata/castAndCrew/personCreate")
	 * @Middleware("auth")
	*/		
	public function personCreate(Request $request)
    {
		if(empty($request->Input('filmId')) || empty($request->Input('persons')) || empty($request->Input('jobs'))){
			return [
				'error' => '1' ,
				'message' => 'Person or Position columns was empty'
			];			
		}
		
		if(!is_numeric($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film Identifier'
			];			
		}
		
		if(count($this->getFilm($request->Input('filmId'))) === 0){
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}		
		
		$filmId = CHhelper::filterInputInt($request->Input('filmId'));
		$JobId = CHhelper::filterInputInt($request->Input('jobs'));
		$personTitle = CHhelper::filterInput($request->Input('persons'));
		
		try{
			$newPersonId = Persons::create([
				'title' => $personTitle 
			])->id;
			
			if($newPersonId){
				FilmsPersons::create([
					'films_id' => $filmId,
					'persons_id' => $newPersonId,
					'jobs_id' => $JobId
				]);

				return [
					'error' => '0' ,
					'message' => 'Person is added'
				];				
			}
		}catch(Exception $e){
			return [
				'error' => '1' ,
				'message' => $e->getMessage()
			];
		}
    }

	/**
	 *@POST("titles/metadata/castAndCrew/getTokenPerson")
	 * @Middleware("auth")
	*/	
	public function getTokenPerson(Request $request)
	{
		if(empty($request->Input('inputToken'))){
			return [
				'error' => '1' ,
				'message' => 'InputToken doesnt exixst'
			];			
		}
		
		$inputToken = trim(filter_var($request->Input('inputToken'),FILTER_SANITIZE_STRING));
		$genre = Persons::where('deleted', '0')->where('title', 'like', $inputToken.'%')->get()->take(20)->toArray();
		array_unshift($genre, ['title' => '<b>'.$inputToken.'</b>']);
		return $genre;
	}	

	/**
	 *@POST("titles/metadata/castAndCrew/getTokenJobs")
	 * @Middleware("auth")
	*/	
	public function getTokenJobs(Request $request)
	{
		if(empty($request->Input('inputToken'))){
			return [
				'error' => '1' ,
				'message' => 'InputToken doesnt exixst'
			];			
		}
		
		$inputToken = trim(filter_var($request->Input('inputToken'),FILTER_SANITIZE_STRING));
		return Jobs::where('deleted', '0')->where('title', 'like', $inputToken.'%')->get()->take(20);
	}
	
	/**
	 *@POST("titles/metadata/castAndCrew/getNewPersonForm")
	 * @Middleware("auth")
	*/		
	public function getNewPersonForm(Request $request)
	{
		if(empty($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Person or Position columns was empty'
			];			
		}
		
		if(!is_numeric($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film Identifier'
			];			
		}
		
		if(count($this->getFilm($request->Input('filmId'))) === 0){
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}
		
		$filmId = CHhelper::filterInputInt($request->Input('filmId'));
		$film = $this->getFilm($filmId);
		
		return view('titles.titleMenegment.metadata.partials.castAndCrew.forms.newPersonForm', compact('film'));
	}	
	
	/**
	 *@POST("titles/metadata/castAndCrew/getPersonEditForm")
	 * @Middleware("auth")
	*/		
	public function getPersonEditForm(Request $request)
	{
		if(empty($request->Input('filmId')) || empty($request->Input('personId'))){
			return [
				'error' => '1' ,
				'message' => 'Person Identifier doesnt exist'
			];			
		}
		
		if(!is_numeric($request->Input('filmId')) || !is_numeric($request->Input('personId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film or personId Identifier'
			];			
		}
		
		if(count($this->getFilm($request->Input('filmId'))) === 0){
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}
		
		$filmId = CHhelper::filterInputInt($request->Input('filmId'));		
		$personId = CHhelper::filterInputInt($request->Input('personId'));		
		
		$thisPerson = Persons::where('id', $personId)->where('deleted', '0')->get();
		$LocalePersons = LocalePersons::where('persons_id', $personId)->get();
		$allLocales = $this->getAllLocale();
		$allUniqueLocales = CHhelper::getUniqueLocale($allLocales, $LocalePersons);
		
		return view('titles.titleMenegment.metadata.partials.castAndCrew.forms.editPersonForm', compact('thisPerson', 'LocalePersons', 'allLocales', 'allUniqueLocales'));
	}

	/**
	 *@POST("titles/metadata/castAndCrew/personAddNewLocale")
	 * @Middleware("auth")
	*/		
	public function personAddNewLocale(Request $request)
	{
		if(empty($request->Input('filmId')) || empty($request->Input('personId')) || empty($request->Input('locale'))){
			return [
				'error' => '1' ,
				'message' => 'Person Identifier or locale doesnt exist'
			];			
		}
		
		if(!is_numeric($request->Input('filmId')) || !is_numeric($request->Input('personId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film or personId Identifier'
			];			
		}
		
		if(count($this->getFilm($request->Input('filmId'))) === 0){
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}
		if(!array_key_exists($request->Input('locale'), $this->getAllLocale())){
			return [
				'error' => '1' ,
				'message' => 'Invalid locale'
			];			
		}
		
		try{
			$filmId = CHhelper::filterInputInt($request->Input('filmId'));		
			$personId = CHhelper::filterInputInt($request->Input('personId'));
			$locale = CHhelper::filterInput($request->Input('locale'));
			$newPersonLocale = LocalePersons::create([
				'persons_id' => $personId,
				'locale' => $locale,
			])->id;
			if($newPersonLocale > 0){
				return [
					'error' => '0' ,
					'message' => 'Person locale is added'
				];				
			}else
				return [
					'error' => '1',
					'message' => 'Mysql Server Error'
				];
		}catch(Exception $e){
			return [
				'error' => '1' ,
				'message' => $e->getMessage()
			];
		}
	}	
	
	/**
	 *@POST("titles/metadata/castAndCrew/removePersonLocale")
	 * @Middleware("auth")
	*/		
	public function removePersonLocale(Request $request)
	{
		if(empty($request->Input('filmId')) || empty($request->Input('localeId'))){
			return [
				'error' => '1' ,
				'message' => 'Person Identifier or locale doesnt exist'
			];			
		}
		
		if(!is_numeric($request->Input('filmId')) || !is_numeric($request->Input('localeId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film or personId Identifier'
			];			
		}
		
		if(count($this->getFilm($request->Input('filmId'))) === 0){
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}		
		
		$localeId = CHhelper::filterInputInt($request->Input('localeId'));

        return LocalePersons::destroy($localeId);				
	}	
	
	/**
	 *@POST("titles/metadata/castAndCrew/personRemove")
	 * @Middleware("auth")
	*/	
    public function personRemove(Request $request)
    {
		if(empty($request->Input('filmId')) || empty($request->Input('personId'))){
			return [
				'error' => '1' ,
				'message' => 'Person Identifier or locale doesnt exist'
			];			
		}
		
		if(!is_numeric($request->Input('filmId')) || !is_numeric($request->Input('personId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film or personId Identifier'
			];			
		}
		
		if(count($this->getFilm($request->Input('filmId'))) === 0){
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}
				
		$personId = CHhelper::filterInputInt($request->Input('personId'));
		
		$personRemove = Persons::where('id', $personId)->update([
			'deleted' => '1'
		]);
		
		if($personRemove > 0){
			LocalePersons::where('persons_id', $personId)->delete();
			return [
				'error' => '0' ,
				'message' => 'Person is deleted'
			];				
		}else
			return [
				'error' => '1' ,
				'message' => 'Mysql Server Error'
			];
    }

	/**
	 *@POST("titles/metadata/castAndCrew/personEdit")
	 * @Middleware("auth")
	*/	
    public function personEdit(Request $request)
    {
		if(empty($request->Input('filmId')) || empty($request->Input('personId'))){
			return [
				'error' => '1' ,
				'message' => 'Person or film Identifier doesnt exist'
			];			
		}
		
		if(!is_numeric($request->Input('filmId')) || !is_numeric($request->Input('personId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film or personId Identifier'
			];			
		}
		
		if(count($this->getFilm($request->Input('filmId'))) === 0){
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}		
		
		try{
			$filmId = CHhelper::filterInputInt($request->Input('filmId'));		
			$personId = CHhelper::filterInputInt($request->Input('personId'));
			
			$title = CHhelper::filterInput($request->Input('title'));
			$brief = CHhelper::filterInput($request->Input('brief'));
			$personImg = CHhelper::filterInput($request->Input('personImage'));

			Persons::where('id', $personId)->where('deleted', '0')->update(array(
				'title' => $title,
				'brief' => $brief,
				'img' => $personImg
			));
			
			if(!empty($request->Input('persons'))){
				foreach($request->Input('persons') as $key => $val) {
					$localeId = CHhelper::filterInputInt($val['localeId']);
					$title = CHhelper::filterInput($val['title']);
					$brief = CHhelper::filterInput($val['brief']);
					
					$localeUpdate =  LocalePersons::where('id', $localeId)->update(array(
						'title' => $title,
						'brief' => $brief
					));		
				}			
			}
			
			return [
				'error' => '0' ,
				'message' => 'Person is updated'
			];			
			
		}catch(Exception $e){
			return [
				'error' => '1' ,
				'message' => $e->getMessage()
			];
		}
    }

	/**
	 *@POST("titles/metadata/castAndCrew/personImageUpload")
	 * @Middleware("auth")
	*/	
    public function personImageUpload(Request $request)
    {
		if(empty($request->Input('filmId')) || empty($request->Input('personId'))){
			return [
				'error' => '1' ,
				'message' => 'Person or film Identifier doesnt exist'
			];			
		}
		
		if(!is_numeric($request->Input('filmId')) || !is_numeric($request->Input('personId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film or personId Identifier'
			];			
		}
		
		if(count($this->getFilm($request->Input('filmId'))) === 0){
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}		
		
		$filmId = CHhelper::filterInputInt($request->Input('filmId'));		
		$personId = CHhelper::filterInputInt($request->Input('personId'));
		
		$s3AccessKey = 'AKIAJPIY5AB3KDVIDPOQ';
		$s3SecretKey = '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';
		$region    	 = 'us-east-1';		 
		$bucket		 = 'cinecliq.assets';		
				
		$fileTypes = array('jpg','jpeg','png','PNG','JPG','JPEG','svg','SVG'); // File extensions
		$size = 500*1024;
		
		$s3path = $request->file('Filedata');
		$s3name = $s3path->getClientOriginalName();
		$s3mimeType = $s3path->getClientOriginalExtension();
		$s3fileSize = $s3path->getClientSize();


		list($_width, $_height, $_type) = @getimagesize($request->file('Filedata'));
		
		if(in_array($s3mimeType, $fileTypes)){
			if($s3fileSize <= $size){
				if($_width <= 750 && $_width >= 375){
					if($_height <= 750 && $_height >= 375){
						
						$s3 = AWS::factory([
							'key'    => $s3AccessKey,
							'secret' => $s3SecretKey,
							'region' => $region,
						])->get('s3');	

						$response = $s3->putObject([
							'Bucket' => $bucket,
							'Key'    => 'persons/'.$s3name,
							'Body'   => fopen($s3path, 'r'),			
							'SourceFile' => $s3path,
							'ACL'    => 'public-read',
						]);	
						
						return  [
									'error' => 0,
									'message' => $s3name
								];
					}else
						$response = [
							'error' => 1,
							'message' => 'Your image could not be uploaded as it does not have the correct aspect ration of 1:1'
						];
				}else
					$response = [
						'error' => 1,
						'message' => 'Your image could not be uploaded as it does not have the correct aspect ration of 1:1'
					];
			}else
				$response = [
					'error' => 1,
					'message' => 'Your image could not be uploaded as the file size exceeds '.($size/1024).'KB.'
				];
		}else
			$response = [
				'error' => 1,
				'message' => $s3mimeType.' is invalid image type'
			];
				
		return $response;
    }
	
	private function getImages($id)
	{
		
		$localeFilms = LocaleFilms::where('films_id', $this->id)->where('deleted', 0)->orderBy('def', 'desc')->get()->toArray();

		return compact('localeFilms');	
	}
	
	/**
	 *@POST("titles/metadata/castAndCrew/posterImageUpload")
	 * @Middleware("auth")
	*/	
    public function posterImageUpload(Request $request)
    {
		if(empty($request->Input('filmId')) || empty($request->Input('locale'))){
			return [
				'error' => '1' ,
				'message' => 'Film Identifier or film locale doesnt exixst'
			];			
		}
		if(!is_numeric($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film Identifier'
			];			
		}

		if(count($this->getFilm($request->Input('filmId'))) === 0){
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}
		
		if(!array_key_exists($request->Input('locale'), $this->getAllLocale())){
			return [
				'error' => '1' ,
				'message' => 'Invalid locale'
			];			
		}
		
		$filmId = CHhelper::filterInputInt($request->Input('filmId'));
		$locale = CHhelper::filterInput($request->Input('locale'));		
		
		$s3AccessKey = 'AKIAJPIY5AB3KDVIDPOQ';
		$s3SecretKey = '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';
		$region    	 = 'us-east-1';		 
		$bucket		 = 'cinecliq.assets';		
				
		$fileTypes = array('jpg','jpeg','png','PNG','JPG','JPEG','svg','SVG'); // File extensions
		$size = 500*1024;
		
		$s3path = $request->file('Filedata');
		$s3name = $s3path->getClientOriginalName();
		$s3mimeType = $s3path->getClientOriginalExtension();
		$s3fileSize = $s3path->getClientSize();


		list($_width, $_height, $_type) = @getimagesize($request->file('Filedata'));
		
		if(in_array($s3mimeType, $fileTypes)){
			if($s3fileSize <= $size){
				if($_width/$_height === 2/3){
					if($_width <= 800 && $_width >= 400){
						if($_height <= 1200 && $_height >= 600){
							
							$s3 = AWS::factory([
								'key'    => $s3AccessKey,
								'secret' => $s3SecretKey,
								'region' => $region,
							])->get('s3');	

							$response = $s3->putObject([
								'Bucket' => $bucket,
								'Key'    => 'files/'.$s3name,
								'Body'   => fopen($s3path, 'r'),			
								'SourceFile' => $s3path,
								'ACL'    => 'public-read',
							]);	
							
							LocaleFilms::Where('films_id', $filmId)->where('locale', $locale)->update(array(
								'cover' => $s3name,
							));
							
							return  [
										'error' => 0,
										'message' => $s3name
									];
						}else
							$response = [
								'error' => 1,
								'message' => 'Your image could not be uploaded as it does not have the correct aspect height'
							];
					}else
						$response = [
							'error' => 1,
							'message' => 'Your image could not be uploaded as it does not have the correct aspect width'
						];					
				}else
					$response = [
						'error' => 1,
						'message' => 'Your image could not be uploaded as it does not have the correct aspect ration of 2:3'
					];					
			}else
				$response = [
					'error' => 1,
					'message' => 'Your image could not be uploaded as the file size exceeds '.($size/1024).'KB.'
				];
		}else
			$response = [
				'error' => 1,
				'message' => $s3mimeType.' is invalid image type'
			];
				
		return $response;
		
    }

	/**
	 *@POST("titles/metadata/castAndCrew/posterImageRemove")
	 * @Middleware("auth")
	*/		
	public function posterImageRemove(Request $request)
	{
		if(empty($request->Input('filmId')) || empty($request->Input('localeId'))){
			return [
				'error' => '1' ,
				'message' => 'Film or locale Identifier doesnt exixst'
			];			
		}
		if(!is_numeric($request->Input('filmId')) || !is_numeric($request->Input('localeId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film or locale Identifier'
			];			
		}
		
		if(count($this->getFilm($request->Input('filmId'))) === 0){
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}
		
		$filmId = CHhelper::filterInputInt($request->Input('filmId'));
		$localeId = CHhelper::filterInput($request->Input('localeId'));
		
		$removeCover = LocaleFilms::Where('id', $localeId)->update(array(
			'cover' => '',
		));	
		if($removeCover > 0)
			return [
				'error' => '0',
				'message' => 'Film Poster Image was deleted successfully'
			];
		else
			return [
				'error' => '1',
				'message' => "Film Poster image doesn't exist"
			];
	}

	/**
	 *@POST("titles/metadata/castAndCrew/tsplashImageUpload")
	 * @Middleware("auth")
	*/		
    public function tsplashImageUpload(Request $request)
    {
		if(empty($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Film Identifier or film locale doesnt exixst'
			];			
		}
		if(!is_numeric($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film Identifier'
			];			
		}

		if(count($this->getFilm($request->Input('filmId'))) === 0){
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}
		
		$filmId = CHhelper::filterInputInt($request->Input('filmId'));
		
		$s3AccessKey = 'AKIAJPIY5AB3KDVIDPOQ';
		$s3SecretKey = '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';
		$region    	 = 'us-east-1';		 
		$bucket		 = 'cinecliq.assets';		
				
		$fileTypes = array('jpg','jpeg','png','PNG','JPG','JPEG','svg','SVG'); // File extensions
		$size = 500*1024;
		$width = 1920;
		$height = 1080;
		
		$s3path = $request->file('Filedata');
		$s3name = $s3path->getClientOriginalName();
		$s3mimeType = $s3path->getClientOriginalExtension();
		$s3fileSize = $s3path->getClientSize();
		

		list($_width, $_height, $_type) = @getimagesize($request->file('Filedata'));
		
		if(in_array($s3mimeType, $fileTypes)){
			if($s3fileSize <= $size){					
				if($_width <= $width){
					if($_height <= $height){
						
						$s3 = AWS::factory([
							'key'    => $s3AccessKey,
							'secret' => $s3SecretKey,
							'region' => $region,
						])->get('s3');	

						$response = $s3->putObject([
							'Bucket' => $bucket,
							'Key'    => 'splash/'.$s3name,
							'Body'   => fopen($s3path, 'r'),			
							'SourceFile' => $s3path,
							'ACL'    => 'public-read',
						]);	
						
						Film::Where('id', $filmId)->update(array(
							'tsplash' => $s3name,
						));
						
						return  [
									'error' => 0,
									'message' => $s3name
								];
					}else
						$response = [
							'error' => 1,
							'message' => 'Your image could not be uploaded as it does not have the correct aspect height'
						];
				}else
					$response = [
						'error' => 1,
						'message' => 'Your image could not be uploaded as it does not have the correct aspect width'
					];
			}else
				$response = [
					'error' => 1,
					'message' => 'Your image could not be uploaded as the file size exceeds '.($size/1024).'KB.'
				];
		}else
			$response = [
				'error' => 1,
				'message' => $s3mimeType.' is invalid image type'
			];
				
		return $response;
		
    }
	
	/**
	 *@POST("titles/metadata/castAndCrew/tsplashImageRemove")
	 * @Middleware("auth")
	*/		
    public function tsplashImageRemove(Request $request)
	{
		if(empty($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Film Identifier doesnt exixst'
			];			
		}
		if(!is_numeric($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film Identifier'
			];			
		}

		if(count($this->getFilm($request->Input('filmId'))) === 0){
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}
		
		$filmId = CHhelper::filterInputInt($request->Input('filmId'));
		
		$removeCover = Film::Where('id', $filmId)->update(array(
			'tsplash' => '',
		));	
		
		if($removeCover > 0)
			return [
				'error' => '0',
				'message' => 'Trailer Splash Image was deleted successfully'
			];
		else
			return [
				'error' => '1',
				'message' => "Trailer Splash image doesn't exist"
			];		
	}	
	
	
	/**
	 *@POST("titles/metadata/castAndCrew/fsplashImageUpload")
	 * @Middleware("auth")
	*/		
    public function fsplashImageUpload(Request $request)
    {
		if(empty($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Film Identifier or film locale doesnt exixst'
			];			
		}
		if(!is_numeric($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film Identifier'
			];			
		}

		if(count($this->getFilm($request->Input('filmId'))) === 0){
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}
		
		$filmId = CHhelper::filterInputInt($request->Input('filmId'));
		
		$s3AccessKey = 'AKIAJPIY5AB3KDVIDPOQ';
		$s3SecretKey = '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';
		$region    	 = 'us-east-1';		 
		$bucket		 = 'cinecliq.assets';		
				
		$fileTypes = array('jpg','jpeg','png','PNG','JPG','JPEG','svg','SVG'); // File extensions
		$size = 500*1024;
		$width = 1920;
		$height = 1080;
		
		$s3path = $request->file('Filedata');
		$s3name = $s3path->getClientOriginalName();
		$s3mimeType = $s3path->getClientOriginalExtension();
		$s3fileSize = $s3path->getClientSize();
		

		list($_width, $_height, $_type) = @getimagesize($request->file('Filedata'));
		
		if(in_array($s3mimeType, $fileTypes)){
			if($s3fileSize <= $size){
				if($_width <= $width){
					if($_height <= $height){
						
						$s3 = AWS::factory([
							'key'    => $s3AccessKey,
							'secret' => $s3SecretKey,
							'region' => $region,
						])->get('s3');	

						$response = $s3->putObject([
							'Bucket' => $bucket,
							'Key'    => 'splash/'.$s3name,
							'Body'   => fopen($s3path, 'r'),			
							'SourceFile' => $s3path,
							'ACL'    => 'public-read',
						]);	
						
						Film::Where('id', $filmId)->update(array(
							'fsplash' => $s3name,
						));
						
						return  [
									'error' => 0,
									'message' => $s3name
								];
					}else
						$response = [
							'error' => 1,
							'message' => 'Your image could not be uploaded as it does not have the correct aspect height'
						];
				}else
					$response = [
						'error' => 1,
						'message' => 'Your image could not be uploaded as it does not have the correct aspect width'
					];
			}else
				$response = [
					'error' => 1,
					'message' => 'Your image could not be uploaded as the file size exceeds '.($size/1024).'KB.'
				];
		}else
			$response = [
				'error' => 1,
				'message' => $s3mimeType.' is invalid image type'
			];
				
		return $response;
		
    }
	
	/**
	 *@POST("titles/metadata/castAndCrew/fsplashImageRemove")
	 * @Middleware("auth")
	*/		
    public function fsplashImageRemove(Request $request)
	{
		if(empty($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Film Identifier doesnt exixst'
			];			
		}
		if(!is_numeric($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film Identifier'
			];			
		}

		if(count($this->getFilm($request->Input('filmId'))) === 0){
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}
		
		$filmId = CHhelper::filterInputInt($request->Input('filmId'));
		
		$removeCover = Film::Where('id', $filmId)->update(array(
			'fsplash' => '',
		));	
		
		if($removeCover > 0)
			return [
				'error' => '0',
				'message' => 'Film Splash Image was deleted successfully'
			];
		else
			return [
				'error' => '1',
				'message' => "Film Splash image doesn't exist"
			];		
	}
	
    private function getSubtitles($id)
    {
		$filmSubtitles = $this->getFilmSubtitles($id);
		$trailerSubtitles = $this->getTrailerSubtitles($id);
		
		return compact('filmSubtitles', 'trailerSubtitles');
    }	
	
    private function getFilmSubtitles($id)
    {
        return FilmSubtitles::where('films_id', $id)->where('deleted', '0')->orderBy('id', 'desc')->get();
    }
	
    private function getTrailerSubtitles($id)
    {
		return TrailerSubtitles::where('films_id', $id)->where('deleted', '0')->orderBy('id', 'desc')->get();
    }

	/**
	 *@POST("titles/metadata/subtitles/subtitlesSaveChanges")
	 * @Middleware("auth")
	*/	
    public function subtitlesSaveChanges(Request $request)
    {
		if(empty($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Film Identifier doesnt exixst'
			];			
		}
		if(!is_numeric($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film Identifier'
			];			
		}

		if(count($this->getFilm($request->Input('filmId'))) === 0){
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}
		
		$filmId = CHhelper::filterInputInt($request->Input('filmId'));
		
		if(!empty($request->Input('subtitleNames')) && is_array($request->Input('subtitleNames'))){
			foreach($request->Input('subtitleNames') as $key => $value){
				$filmSubtitleId = CHhelper::filterInputInt($key);
				$file = CHhelper::filterInput($request->Input('fsubtitleFile_'.$key));
				$title = CHhelper::filterInput($value);
				
				FilmSubtitles::where('id', $filmSubtitleId)->update([
					'title' => $title,
					'file' => $filmId.'/'.'f/'.$file,
				]);
			}
		}		
		
		if(!empty($request->Input('tSubtitleNames')) && is_array($request->Input('tSubtitleNames'))){
			foreach($request->Input('tSubtitleNames') as $key => $value){
				$trailerSubtitleId = CHhelper::filterInputInt($key);
				$file = CHhelper::filterInput($request->Input('tsubtitleFile_'.$key));
				$title = CHhelper::filterInput($value);
				
				TrailerSubtitles::where('id', $trailerSubtitleId)->update([
					'title' => $title,
					'file' => $filmId.'/'.'t/'.$file,
				]);
			}
		}
        return [
			'error' => '0',
			'message' => 'Subtitles saved successfully'
		];
    }
	
	/**
	 *@POST("titles/metadata/subtitles/CreateNewFilmSubtitle")
	 * @Middleware("auth")
	*/		
    public function CreateNewFilmSubtitle(Request $request)
    {
		if(empty($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Film Identifier doesnt exixst'
			];			
		}
		if(!is_numeric($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film Identifier'
			];			
		}

		if(count($this->getFilm($request->Input('filmId'))) === 0){
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}
		
		$filmId = CHhelper::filterInputInt($request->Input('filmId'));
		$filmSubTitle = CHhelper::filterInput($request->Input('filmSubTitle'));
		$file = CHhelper::filterInput($request->Input('fsubtitleFile'));
		
		$newfilmSubtitleId = FilmSubtitles::create([
			'title'    =>  $filmSubTitle,
			'file'     =>  $filmId.'/'.'f/'.$file,
			'films_id' =>  $filmId,
		])->id;

		if($newfilmSubtitleId > 0)
			return [
				'error' => '0',
				'message' => 'Film new subtitle was created successfully'
			];
		else
			return [
				'error' => '1',
				'message' => "Film new subtitle doesn't created"
			];		
    }
	
	/**
	 *@POST("titles/metadata/subtitles/removeFilmSubtitle")
	 * @Middleware("auth")
	*/		
    public function removeFilmSubtitle(Request $request)
    {
		if(empty($request->Input('filmId')) || empty($request->Input('subTitleId'))){
			return [
				'error' => '1' ,
				'message' => 'Film Identifier doesnt exixst'
			];			
		}
		if(!is_numeric($request->Input('filmId')) || !is_numeric($request->Input('subTitleId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film Identifier'
			];			
		}

		if(count($this->getFilm($request->Input('filmId'))) === 0){
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}
		
		$subTitleId = CHhelper::filterInputInt($request->Input('subTitleId'));

		$removeFilmSubtitle = FilmSubtitles::where('id', $subTitleId)->update([
			'deleted' => '1',
		]);

		if($removeFilmSubtitle > 0)
			return [
				'error' => '0',
				'message' => 'Film subtitle was deleted successfully'
			];
		else
			return [
				'error' => '1',
				'message' => "Film subtitle doesn't deleted"
			];		
    }    

	/**
	 *@POST("titles/metadata/subtitles/CreateNewTrailerSubtitle")
	 * @Middleware("auth")
	*/		
    public function CreateNewTrailerSubtitle(Request $request)
    {
		if(empty($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Film Identifier doesnt exixst'
			];			
		}
		if(!is_numeric($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film Identifier'
			];			
		}

		if(count($this->getFilm($request->Input('filmId'))) === 0){
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}
		
		$filmId = CHhelper::filterInputInt($request->Input('filmId'));
		$trailerSubTitle = CHhelper::filterInput($request->Input('trailerSubTitle'));
		$trailerfile = CHhelper::filterInput($request->Input('tsubtitleFile'));
		
		$newTrailerSubtitleId = TrailerSubtitles::create([
			'title'    =>  $trailerSubTitle,
			'file'     =>  $filmId.'/'.'f/'.$trailerfile,
			'films_id' =>  $filmId,
		])->id;

		if($newTrailerSubtitleId > 0)
			return [
				'error' => '0',
				'message' => 'Trailer new subtitle was created successfully'
			];
		else
			return [
				'error' => '1',
				'message' => "Trailer new subtitle doesn't created"
			];				
    }
	
	/**
	 *@POST("titles/metadata/subtitles/removeTrailerSubtitle")
	 * @Middleware("auth")
	*/		
    public function removeTrailerSubtitle(Request $request)
    {
		if(empty($request->Input('filmId')) || empty($request->Input('trailerSubTitleId'))){
			return [
				'error' => '1' ,
				'message' => 'Film Identifier doesnt exixst'
			];			
		}
		if(!is_numeric($request->Input('filmId')) || !is_numeric($request->Input('trailerSubTitleId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film Identifier'
			];			
		}

		if(count($this->getFilm($request->Input('filmId'))) === 0){
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}
		
		$tSubTitleId = CHhelper::filterInputInt($request->Input('trailerSubTitleId'));

		$removeFilmSubtitle = TrailerSubtitles::where('id', $tSubTitleId)->update([
			'deleted' => '1',
		]);

		if($removeFilmSubtitle > 0)
			return [
				'error' => '0',
				'message' => 'Trailer subtitle was deleted successfully'
			];
		else
			return [
				'error' => '1',
				'message' => "Trailer subtitle doesn't deleted"
			];		
    }

	/**
	 *@POST("titles/metadata/subtitles/uploadFile")
	 * @Middleware("auth")
	*/		
    public function uploadFile(Request $request)
    {
		if(empty($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Film Identifier doesnt exixst'
			];			
		}
		if(!is_numeric($request->Input('filmId'))){
			return [
				'error' => '1' ,
				'message' => 'Invalid Film Identifier'
			];			
		}

		if(count($this->getFilm($request->Input('filmId'))) === 0){
			return [
				'error' => '1' ,
				'message' => 'You dont have perrmisions'
			];			
		}
		
		$filmId = CHhelper::filterInputInt($request->Input('filmId'));
		$fileName = CHhelper::filterInput($request->Input('fileName'));
		
		$s3AccessKey = 'AKIAJPIY5AB3KDVIDPOQ';
		$s3SecretKey = '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';
		$region    	 = 'us-east-1';		 
		$bucket		 = 'cinecliq.assets';		
				
		$fileTypes = array('srt','SRT'); // File extensions
		
		$s3path = $request->file('Filedata');
		$s3name = $s3path->getClientOriginalName();
		$s3mimeType = $s3path->getClientOriginalExtension();
		$s3fileSize = $s3path->getClientSize();
		

		list($_width, $_height, $_type) = @getimagesize($request->file('Filedata'));
		
		if(in_array($s3mimeType, $fileTypes)){		
			$s3 = AWS::factory([
				'key'    => $s3AccessKey,
				'secret' => $s3SecretKey,
				'region' => $region,
			])->get('s3');	

			$response = $s3->putObject([
				'Bucket' => $bucket,
				'Key'    => 'subtitles/'.$filmId.'/'.$fileName.'/'.$s3name,
				'Body'   => fopen($s3path, 'r'),			
				'SourceFile' => $s3path,
				'ACL'    => 'public-read',
			]);	
			
			return [
				'error' => 0,
				'fileName' => $s3name,
				'message' => 'File was uploaded successfully'
			];			
		}else
			$response = [
				'error' => 1,
				'message' => $s3mimeType.' is invalid file type'
			];
				
		return $response;
	
    }
	
	
	private function getAgeRates($id)
    {
		$film = $this->getFilm($id);
		
		$ageRates = AgeRates::join('cc_countries', 'cc_age_rates.countries_id', '=', 'cc_countries.id')
					->select(array('cc_age_rates.*', 'cc_countries.title as countryTitle', 'cc_countries.id as countryId'))
					->where('cc_age_rates.deleted', '<>', '1')
					->where('cc_countries.deleted', '<>', '1')
					->orderBy('countryTitle', 'asc')
					->get();
		$ageRate=array();
		
		foreach($ageRates as $key => $value){
			$ageRate[$value->countryTitle][] = $value;
		}
		
		$fkAgeRates = FilmsAgeRates::where('films_id', $id)->select('age_rates_id')->get();
		
		$filmRates = array();
		foreach($fkAgeRates as $key){
			$filmRates[$key->age_rates_id] = $key->age_rates_id;
		}
		
		//dd($filmRates);
        //dd($ageRate['Spain'][0]);
		
		return compact('ageRate', 'filmRates');
    }

    public function getSeries($id)
    {
		$film = $this->getFilm($id);
		
		//dd($film->series_parent);
		$parentFilm = Film::where('deleted', '0')->find($film->series_parent);

		return compact('parentFilm');
    } 	
	
	/**
	 *@POST("titles/metadata/seriesSaveChanges")
	 * @Middleware("auth")
	*/				
    public function seriesSaveChanges(Request $request)
    {
		$filmId=trim(filter_var($request->Input('filmId'),FILTER_SANITIZE_NUMBER_INT));
		$seriesParent = trim(filter_var($request->Input('series_parent'),FILTER_SANITIZE_NUMBER_INT));
		$filmType = trim(filter_var($request->Input('filmType'),FILTER_SANITIZE_NUMBER_INT));
		
		if($filmType === -2){
			if(!empty($seriesParent)) {
				$seriesNum = trim(filter_var($request->Input('series_num'),FILTER_SANITIZE_NUMBER_INT));
				$filmType = $seriesParent;
			}else{
				$seriesNum = 0;
				$filmType = trim(filter_var($request->Input('filmType'),FILTER_SANITIZE_NUMBER_INT));
			}			
		}else{
			$seriesNum = 0;
		}
		
		Film::where('id', $filmId)->update([
			'series_parent' => $filmType,
			'series_num' => $seriesNum,
		]);
		
    }	   
	
	/**
	 *@POST("titles/metadata/series/getTokenSeries")
	 * @Middleware("auth")
	*/	
	public function getTokenSeries(Request $request)
	{
		$inputToken = trim(filter_var($request->Input('inputToken'),FILTER_SANITIZE_STRING));
		
		$user_info = Auth::user();
		
        $account_info = $user_info->account;
        //(new Dumper)->dump($account_info->toArray());

        $account_features = $account_info->features;
        //(new Dumper)->dump($account_features->toArray());

        $company_info = $account_info->company;
        //(new Dumper)->dump($company_info->toArray());

        $company_films = $company_info->films()->where('cc_films.series_parent', '-1')->where('cc_films.title', 'like', $inputToken.'%')->get();

        $store_info = $account_info->store;
        //(new Dumper)->dump($store_info->toArray());

        $store_films = $store_info->contracts()->with('films', 'stores')->get()->where('cc_films.series_parent', '-1')->where('cc_films.title', 'like', $inputToken.'%');

        foreach($company_films as $key=>$company_film){
            $company_film_stores = $company_films->first()->baseContract()->with('stores')->get();

            $company_film->stores = $company_film_stores->first()->stores;
            $company_film->companies = $company_film->companies()->where('fk_films_owners.type', '0')->get();
        }


        foreach($store_films as $key=>$store_film){
            $store_film->films->stores = $store_film->stores;
            $store_film->films->companies = $store_film->films->companies()->where('fk_films_owners.type', '0')->get();
            $store_films[$key] = $store_film->films;
            //unset($store_films[$key]->stores);
        }

        $company_films = $company_films->keyBy('id');
        $store_films = $store_films->keyBy('id');
        $films = $company_films->merge($store_films);
		
		return $films;
	}	

	public function getSeo($id)
    {
		$keywords = ChannelsFilmsKeywords::where('films_id', $id)->get()->keyBy('locale');
		$allLocales = $this->getAllLocale();
		$seoAllUniqueLocales = CHhelper::getUniqueLocale($allLocales, $keywords);	
		
		return compact('keywords', 'seoAllUniqueLocales');	
    }
	
	/**
	 *@POST("titles/metadata/castAndCrew/addSeoItem")
	 * @Middleware("auth")
	*/			
    public function addSeoItem(Request $request)
    {
		$filmId=trim(filter_var($request->Input('filmId'),FILTER_SANITIZE_NUMBER_INT));
		
		$locale=trim(filter_var($request->Input('countries'),FILTER_SANITIZE_STRING));
		$keywords=trim(filter_var($request->Input('keywords'),FILTER_SANITIZE_STRING));
		$description=trim(filter_var($request->Input('description'),FILTER_SANITIZE_STRING));
		
		$userInfo = Auth::user();
		$accountInfo = $userInfo->account;		
		$channelId = $accountInfo->platforms_id;

		return ChannelsFilmsKeywords::create([
			'channels_id' => $channelId,
			'films_id' => $filmId,
			'description' => $description,
			'keywords' => $keywords,
			'locale' => $locale,
		])->id;
    }
	
	/**
	 *@POST("titles/metadata/castAndCrew/editSeoItem")
	 * @Middleware("auth")
	*/		
    public function editSeoItem(Request $request)
    {
		$keywordId=trim(filter_var($request->Input('keywordsId'),FILTER_SANITIZE_NUMBER_INT));
		
		$locale=trim(filter_var($request->Input('countries'),FILTER_SANITIZE_STRING));
		$keywords=trim(filter_var($request->Input('keywords'),FILTER_SANITIZE_STRING));
		$description=trim(filter_var($request->Input('description'),FILTER_SANITIZE_STRING));

		return ChannelsFilmsKeywords::where('id', $keywordId)->update([
			'description' => $description,
			'keywords' => $keywords,
			'locale' => $locale,
		]);
    }
	
	/**
	 *@POST("titles/metadata/castAndCrew/removeSeoItem")
	 * @Middleware("auth")
	*/		
    public function removeSeoItem(Request $request)
    {
		$keywordId = trim(filter_var($request->Input('keywordId'),FILTER_SANITIZE_NUMBER_INT));
		return ChannelsFilmsKeywords::destroy($keywordId);
    }

}

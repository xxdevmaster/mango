<?php

namespace App\Http\Controllers\TitleManagement;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use App\Libraries\CHhelper\CHhelper;
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
use App\Models\ChannelsContracts;
use Aws\Common\Aws;

class MetadataController extends Controller
{
	/**
	 * Illuminate\Http\Request
	 * @var Object
	 */
	private $request;

	/**
	 * Auth User
	 * @var object
	 */
	private $authUser;

	/**
	 * Auth User Store Id
	 * @var integer
	 */
	private $storeID;

	/**
	 * Auth User Company Id
	 * @var integer
	 */
	private $companyID;

	/**
	 * Films Locale ID
	 * @var integer
	 */
	private $localeID;

	/**
	 * Films Locale
	 * @var stirng
	 */
	private $locale;

	/**
	 * Films Job Id
	 * @var integer
	 */
	private $JobID;

	/**
	 * Films Person Title
	 * @var string
	 */
	private $personTitle;

	/**
	 * Films Person id
	 * @var Integer
	 */
	private $personID;

	/**
	 * Create A new Variables For Auth User.
	 *
	 * @param  Object  $request
	 * @return void
	 */
	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->authUser = Auth::user();
		$this->storeID = $this->authUser->account->platforms_id;
		$this->companyID = $this->authUser->account->companies_id;
	}

	/**
	 * Show Titles Management Metadata
	 * @param
	 * @return Response Html
	 */
    public function metadataShow()
    {
		$current_menu = 'Metadata';
		$film = $this->request->film;
		$allLocales = $this->getAllLocale();
		
		$metadata = [
			'basic' => $this->getBasic() ,
			'advanced' => $this->getAdvanced() ,
			'castAndCrew' => $this->getCastAndCrew() ,
			'images' => $this->getImages() ,
			'subtitles' => $this->getSubtitles() ,
			'ageRates' => $this->getAgeRates() ,
			'series' => $this->getSeries() ,
			'seo' => $this->getSeo()
		];
		return view('titles.titleManagement.metadata.metadata', compact('current_menu', 'id', 'film', 'allLocales', 'metadata'));
    }

	/**
	 * Get All Locales
	 * @return array
	*/
	private function getAllLocale()
	{
		return AllLocales::lists('title', 'code')->toArray();
	}
	
	/**
	 * Get Template
	 * @param string $templateName
	 * @return Html Response
	*/
	private function getTemplate($templateName)
	{
		$template= '';
		$film = $this->request->film;
		$allLocales = $this->getAllLocale();	
		
		switch($templateName){
			case 'basic' : $template = $this->getBasic($this->request->filmID); break;
			case 'castAndCrew' : $template = $this->getCastAndCrew($this->request->filmID); break;
			case 'images' : $template = $this->getImages($this->request->filmID); break;
			case 'subtitles' : $template = $this->getSubtitles($this->request->filmID); break;
			case 'seo' : $template = $this->getSeo($this->request->filmID); break;
		}
		
		$metadata = [
			$templateName => $template
		];
		
		return view('titles.titleManagement.metadata.partials.'.$templateName.'.'.$templateName, compact('film', 'allLocales', 'metadata'))->render();
	}

	/**
	 * Get Tab Basic
	 * @return Response
	 */
    private function getBasic()
    {			
		$filmLocales = LocaleFilms::where('films_id', $this->request->filmID)->where('deleted', 0)->orderBy('def', 'desc')->orderBy('id', 'asc')->get();
		$allLocales = $this->getAllLocale();
		$allUniqueLocales = CHhelper::getUniqueLocale($allLocales, $filmLocales);
		
		return compact('filmLocales', 'allUniqueLocales');
		
    }

	/**
	 * Saveing Film Changes
	 * @POST("titles/metadata/basicSaveChanges")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	*/
    public function basicSaveChanges()
    {
		$localeArray = (!empty($this->request->Input('filmsLocales')) && is_array($this->request->Input('filmsLocales'))) ? $this->request->Input('filmsLocales') : false;

		if(!$localeArray)
			return [
				'error' => '1' ,
				'message' => 'Invalid argument'
			];

		foreach($localeArray as $locale => $localeInfo)
			if(array_key_exists($locale, $this->getAllLocale()))
				if(!empty($localeInfo['localeId']) && is_numeric($localeInfo['localeId']))
				{
					$localeID = CHhelper::filterInputInt($localeInfo['localeId']);

					if(!empty($localeInfo['title']))
						$title = CHhelper::filterInput($localeInfo['title']);
					else
						$title = '';
					if(!empty($localeInfo['synopsis']))
						$synopsis = CHhelper::filterInput($localeInfo['synopsis']);
					else
						$synopsis = '';

					LocaleFilms::where('id', $localeID)->where('films_id', $this->request->filmID)->update([
						'title' => $title,
						'synopsis' => $synopsis,
					]);

					if($localeInfo['def'] === '1')
						Film::where('id', $this->request->filmID)->update([
							'title' => $title,
							'synopsis' => $synopsis,
						]);
				}

    } 

	/**
	 * Add New Locale To Film
	 * @POST("titles/metadata/basic/newLocale")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	*/
	public function basicAddNewLocale()
    {
		$locale = (!empty($this->request->Input('locale'))) ? CHhelper::filterInput($this->request->Input('locale')) : false;

		if(!array_key_exists($locale, $this->getAllLocale())){
			return [
				'error' => '1' ,
				'message' => 'Invalid locale'
			];			
		}

		$newLocaleID = LocaleFilms::create([
			'films_id' => $this->request->filmID,
			'locale' => $locale,
		])->id;

		if($newLocaleID)

			return [
				'basic' =>  $this->getTemplate('basic'),
				'images' => $this->getTemplate('images')
			];

		return [
			'error' => '1',
			'message' => 'Locale dont created'
		];
    }
	
	/**
	 * Delete Locale From Film
	 * @POST("titles/metadata/basic/localeRemove")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	*/
    public function basicLocaleRemove()
    {
		$localeID = (!empty($this->request->Input('localeID')) && is_numeric($this->request->Input('localeID'))) ? CHhelper::filterInputInt($this->request->Input('localeID')) : false;

		if(!$localeID){
			return [
				'error' => '1' ,
				'message' => 'Invalid argument locale id'
			];
		}

		LocaleFilms::where('id', $localeID)->where('films_id', $this->request->filmID)->where('def', '<>', '1')->update([
			'deleted' => 1
		]);

		return [
			'basic' =>  $this->getTemplate('basic'),
			'images' => $this->getTemplate('images')
		];
    }   

	/**
	 * make default Locale From Film
	 * @POST("titles/metadata/basic/makeDefaultLocale")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	*/
	public function makeDefaultLocale()
    {
		$this->localeID = (!empty($this->request->Input('localeID')) && is_numeric($this->request->Input('localeID'))) ? CHhelper::filterInputInt($this->request->Input('localeID')) : false;
		$this->locale = (!empty($this->request->Input('locale'))) ? CHhelper::filterInput($this->request->Input('locale')) : false;

		if(!array_key_exists($this->locale, $this->getAllLocale()) || !$this->localeID){
			return [
				'error' => '1' ,
				'message' => 'Invalid argument locale or locale id'
			];
		}

		DB::transaction(function() {
			LocaleFilms::where('films_id', $this->request->filmID)->where('def', '1')->update([
				'def' => 0
			]);

			LocaleFilms::where('films_id', $this->request->filmID)->where('id', $this->localeID)->update([
				'def' => 1
			]);

			Film::where('id', $this->request->filmID)->update([
				'i18n' => $this->locale ,
				'locale' => $this->locale
			]);
		});

		return [
			'basic' =>  $this->getTemplate('basic'),
			'images' => $this->getTemplate('images')
		];
    }

	/**
	 * Get Tab Advanced
	 * @@return array
	 */
	private function getAdvanced()
	{
		$film = $this->request->film;

		$filmGenres = $film->genres()->get()->keyBy('id');
		$filmLanguages = $film->languages()->get()->keyBy('id');
		$filmProdCompanies = $film->prodCompanies()->get()->keyBy('id');
		$filmCountries = $film->countries()->get()->keyBy('id');

		return compact('filmGenres', 'filmLanguages', 'filmProdCompanies', 'filmCountries');
	}

	/**
	 * Get Token Genres
	 * @POST("titles/metadata/advanced/getTokenGenres")
	 * @Middleware("auth")
	 * @return collaction
	*/	
	public function getTokenGenres()
	{
		$inputToken = (!empty($this->request->Input('inputToken'))) ? CHhelper::filterInput($this->request->Input('inputToken')) : false;
		return Genres::where('deleted', '0')->where('title', 'like', $inputToken.'%')->get();		
	}	

	/**
	 * Get Token Original Languages
	 * @POST("titles/metadata/advanced/getTokenOriginalLanguages")
	 * @Middleware("auth")
	 * @return collaction
	*/	
	public function getTokenOriginalLanguages()
	{
		$inputToken = (!empty($this->request->Input('inputToken'))) ? CHhelper::filterInput($this->request->Input('inputToken')) : false;
		return Languages::where('deleted', '0')->where('title', 'like', $inputToken.'%')->get(); 
	}	
	
	/**
	 * Get Token Original Production Companies
	 * @POST("titles/metadata/advanced/getTokenProdCompanies")
	 * @Middleware("auth")
	 * @return collaction
	*/	
	public function getTokenProdCompanies()
	{
		$inputToken = (!empty($this->request->Input('inputToken'))) ? CHhelper::filterInput($this->request->Input('inputToken')) : false;
		return ProdCompanies::where('deleted', '0')->where('title', 'like', $inputToken.'%')->get();		 
	}	
	
	/**
	 * Get Token Original Countries
	 * @POST("titles/metadata/advanced/getTokenCountries")
	 * @Middleware("auth")
	 * @return collaction
	*/	
	public function getTokenCountries()
	{
		$inputToken =  (!empty($this->request->Input('inputToken'))) ? CHhelper::filterInput($this->request->Input('inputToken')) : false;
		return Countries::where('deleted', '0')->where('title', 'like', $inputToken.'%')->get();
	}

	/**
	 * Save Advanced changes
	 * @POST("titles/metadata/advancedSaveChanges")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return Response
	*/	
	public function advancedSaveChanges()
	{
		$dt = CHhelper::filterInput($this->request->Input('dt'));
		$duration = CHhelper::filterInput($this->request->Input('duration'));
		$admComment = CHhelper::filterInput($this->request->Input('admcomment'));

		DB::transaction(function() {
			FilmsGenres::where('films_id', $this->request->filmID)->delete();

			if(is_array($this->request->Input('genres')))
				foreach($this->request->Input('genres') as $genresID => $val) {
					if (is_numeric($genresID))
						$genresID = CHhelper::filterInputInt($genresID);
					else continue;
					FilmsGenres::create([
						'films_id' => $this->request->filmID,
						'genres_id' => $genresID,
					]);
				}
		});

		DB::transaction(function() {
			FilmsLanguages::where('films_id', $this->request->filmID)->delete();

			if(is_array($this->request->Input('originalLanguages')))
				foreach($this->request->Input('originalLanguages') as $originalLanguageID => $val) {
					if (is_numeric($originalLanguageID))
						$originalLanguageID = CHhelper::filterInputInt($originalLanguageID);
					else continue;
					FilmsLanguages::create([
						'films_id' => $this->request->filmID,
						'languages_id' => $originalLanguageID,
					]);
				}
		});


		DB::transaction(function() {
			FilmsProdCompanies::where('films_id', $this->request->filmID)->delete();

			if(is_array($this->request->Input('productCompanies')))
				foreach($this->request->Input('productCompanies') as $productCompaniesID => $val) {
					if (is_numeric($productCompaniesID))
						$productCompaniesID = CHhelper::filterInputInt($productCompaniesID);
					else continue;
					FilmsProdCompanies::create([
						'films_id' => $this->request->filmID,
						'prodcompanies_id' => $productCompaniesID,
					]);
				}
		});


		DB::transaction(function() {
			FilmsCountries::where('films_id', $this->request->filmID)->delete();

			if(is_array($this->request->Input('countries')))
				foreach($this->request->Input('countries') as $countryID => $val) {
					if (is_numeric($countryID))
						$countryID = CHhelper::filterInputInt($countryID);
					else continue;
					FilmsCountries::create([
						'films_id' => $this->request->filmID,
						'countries_id' => $countryID,
					]);
				}
		});

		Film::where('id', $this->request->filmID)->update([
			'dt' => $dt,
			'duration' => $duration,
			'admcomment' => $admComment,
		]);

	}

	/**
	 * Get Tab Cast & Crew
	 * @return array
	 */
	private function getCastAndCrew()
	{
		$film = $this->request->film;
		$person = $film->persons()->get();
		return compact('person');
	}

	/**
	 * Get Persons Create Form
	 * @POST("titles/metadata/castAndCrew/getNewPersonForm")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return Response Html
	 */
	public function getNewPersonForm()
	{
		$film = $this->request->film;
		return view('titles.titleManagement.metadata.partials.castAndCrew.forms.newPersonForm', compact('film'));
	}

	/**
	 * Get Token Persons
	 * @POST("titles/metadata/castAndCrew/getTokenPerson")
	 * @Middleware("auth")
	 * @return collection
	 */
	public function getTokenPerson()
	{
		$inputToken = (!empty($this->request->Input('inputToken'))) ? CHhelper::filterInput($this->request->Input('inputToken')) : false;
		$genre = Persons::where('deleted', '0')->where('title', 'like', $inputToken.'%')->get()->take(20)->toArray();
		array_unshift($genre, ['title' => '<b>'.$inputToken.'</b>']);
		return $genre;
	}

	/**
	 *@POST("titles/metadata/castAndCrew/getTokenJobs")
	 * @Middleware("auth")
	 */
	public function getTokenJobs()
	{
		$inputToken = (!empty($this->request->Input('inputToken'))) ? CHhelper::filterInput($this->request->Input('inputToken')) : false;
		return Jobs::where('deleted', '0')->where('title', 'like', $inputToken.'%')->get()->take(20);
	}

	/**
	 * Create New Person
	 * @POST("titles/metadata/castAndCrew/personCreate")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return Response Html
	*/
	public function personCreate()
    {
		$this->JobID = (!empty($this->request->Input('jobID')) && is_numeric($this->request->Input('jobID'))) ? CHhelper::filterInputInt($this->request->Input('jobID')) : false;
		$this->personTitle = (!empty($this->request->Input('persons'))) ? CHhelper::filterInput($this->request->Input('persons')) : false;

		if($this->JobID && $this->personTitle)
			DB::transaction(function(){
				$newPersonId = Persons::create([
					'title' => $this->personTitle
				])->id;

				FilmsPersons::create([
					'films_id' => $this->request->filmID,
					'persons_id' => $newPersonId,
					'jobs_id' => $this->JobID
				]);
			});

		$metadata['castAndCrew']['person'] = $this->request->film->persons()->get();
		return view('titles.titleManagement.metadata.partials.castAndCrew.castAndCrew', compact('metadata'));
    }

	/**
	 * Remove Person
	 * @POST("titles/metadata/castAndCrew/personRemove")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return Response Html
	 */
	public function personRemove()
	{
		$this->personID = (!empty($this->request->Input('personID')) && is_numeric($this->request->Input('personID'))) ? CHhelper::filterInputInt($this->request->Input('personID')) : false;

		if(!$this->personID)
			return [
				'error' => '1',
				'message' => 'Invalid argument person id'
			];

		DB::transaction(function() {
			Persons::where('id', $this->personID)->update([
				'deleted' => '1'
			]);

			LocalePersons::where('persons_id', $this->personID)->delete();
		});

		$metadata['castAndCrew']['person'] = $this->request->film->persons()->get();
		return view('titles.titleManagement.metadata.partials.castAndCrew.castAndCrew', compact('metadata'));
	}

	/**
	 * Get Person Edit Form
	 * @POST("titles/metadata/castAndCrew/getPersonEditForm")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return Response Html
	*/		
	public function getPersonEditForm()
	{
		$this->personID = (!empty($this->request->Input('personID')) && is_numeric($this->request->Input('personID'))) ? CHhelper::filterInputInt($this->request->Input('personID')) : false;
		if(!$this->personID)
			return [
				'error' => '1',
				'message' => 'Invalid argument person id'
			];

		$person = Persons::where('id', $this->personID)->where('deleted', '0')->get();
		$localePersons = LocalePersons::where('persons_id', $this->personID)->get();
		$allLocales = $this->getAllLocale();
		$allUniqueLocales = CHhelper::getUniqueLocale($allLocales, $localePersons);
		return view('titles.titleManagement.metadata.partials.castAndCrew.forms.editPersonForm', compact('person', 'localePersons', 'allLocales', 'allUniqueLocales'));
	}

	/**
	 * Add New Locale For Person
	 * @POST("titles/metadata/castAndCrew/personAddNewLocale")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return Resposne Html
	*/		
	public function personAddNewLocale()
	{
		$this->personID = (!empty($this->request->Input('personID')) && is_numeric($this->request->Input('personID'))) ? CHhelper::filterInputInt($this->request->Input('personID')) : false;
		$locale = (!empty($this->request->Input('locale'))) ? CHhelper::filterInput($this->request->Input('locale')) : false;

		if(!array_key_exists($locale, $this->getAllLocale()) || !$this->personID)
			return [
				'error' => '1' ,
				'message' => 'Invalid argument locale or person id'
			];

		LocalePersons::create([
			'persons_id' => $this->personID,
			'locale' => $locale,
		]);

		return $this->getPersonEditForm();
	}	
	
	/**
	 * Remove Person Locale
	 * @POST("titles/metadata/castAndCrew/removePersonLocale")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return Resposne Html
	*/		
	public function removePersonLocale()
	{
		$localeID = (!empty($this->request->Input('localeID')) && is_numeric($this->request->Input('localeID'))) ? CHhelper::filterInput($this->request->Input('localeID')) : false;
		$this->personID = (!empty($this->request->Input('personID')) && is_numeric($this->request->Input('personID'))) ? CHhelper::filterInputInt($this->request->Input('personID')) : false;

		if(!$localeID || !$this->personID)
			return [
				'error' => '1' ,
				'message' => 'Invalid argument locale id or person id'
			];

        LocalePersons::destroy($localeID);

		return $this->getPersonEditForm();
	}

	/**
	 * Save Person Changes
	 * @POST("titles/metadata/castAndCrew/personEdit")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return Response Html
	*/	
    public function personEdit()
    {
		$this->personID = (!empty($this->request->Input('personID')) && is_numeric($this->request->Input('personID'))) ? CHhelper::filterInputInt($this->request->Input('personID')) : false;
		$localesPerson = (!empty($this->request->Input('persons')) && is_array($this->request->Input('persons'))) ? $this->request->Input('persons') : false;

		$this->title = CHhelper::filterInput($this->request->Input('title'));
		$this->brief = CHhelper::filterInput($this->request->Input('brief'));
		$this->personImg = CHhelper::filterInput($this->request->Input('personImage'));

		DB::transaction(function() {
			Persons::where('id', $this->personID)->where('deleted', '0')->update([
				'title' => $this->title,
				'brief' => $this->brief,
				'img' => $this->personImg
			]);
		});

		foreach($localesPerson as $personInfo) {
			$this->localeID = CHhelper::filterInputInt($personInfo['localeID']);
			$this->title = CHhelper::filterInput($personInfo['title']);
			$this->brief = CHhelper::filterInput($personInfo['brief']);

			LocalePersons::where('id', $this->localeID)->update([
				'title' => $this->title,
				'brief' => $this->brief
			]);
		}

		$metadata['castAndCrew']['person'] = $this->request->film->persons()->get();
		return view('titles.titleManagement.metadata.partials.castAndCrew.castAndCrew', compact('metadata'));
    }

	/**
	 * Upload Images From Person
	 * @POST("titles/metadata/castAndCrew/personImageUpload")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return array
	*/	
    public function personImageUpload()
    {
		$this->personID = (!empty($this->request->Input('personID')) && is_numeric($this->request->Input('personID'))) ? CHhelper::filterInputInt($this->request->Input('personID')) : false;

		$s3AccessKey = 'AKIAJPIY5AB3KDVIDPOQ';
		$s3SecretKey = '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';
		$region    	 = 'us-east-1';		 
		$bucket		 = 'cinecliq.assets';		
				
		$fileTypes = array('jpg','jpeg','png','PNG','JPG','JPEG','svg','SVG'); // File extensions
		$size = 500*1024;

		$s3path = $this->request->file('Filedata');
		$s3name = $s3path->getClientOriginalName(); // File Name
		$s3mimeType = $s3path->getClientOriginalExtension(); // File Mime Type
		$s3fileSize = $s3path->getClientSize();// Max File Size

		list($_width, $_height) = @getimagesize($this->request->file('Filedata'));
		
		if(in_array($s3mimeType, $fileTypes)){
			if($s3fileSize <= $size){
				if($_width <= 750 && $_width >= 375){
					if($_height <= 750 && $_height >= 375){
						
						$s3 = Aws::factory([
							'key'    => $s3AccessKey,
							'secret' => $s3SecretKey,
							'region' => $region,
						])->get('s3');	

						$s3->putObject([
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
					}
				}
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

	/**
	 * Get Tab Images
	 * @return array
	 */
	private function getImages()
	{
		$localeFilms = LocaleFilms::where('films_id', $this->request->filmID)->where('deleted', 0)->orderBy('def', 'desc')->get()->toArray();

		return compact('localeFilms');	
	}
	
	/**
	 * Upload Poster Image
	 * @POST("titles/metadata/castAndCrew/posterImageUpload")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return array
	*/	
    public function posterImageUpload()
    {
		$locale = (!empty($this->request->Input('locale'))) ? CHhelper::filterInput($this->request->Input('locale')) : false;

		if(!array_key_exists($locale, $this->getAllLocale()))
			return [
				'error' => '1' ,
				'message' => 'Invalid argument locale'
			];
		
		$s3AccessKey = 'AKIAJPIY5AB3KDVIDPOQ';
		$s3SecretKey = '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';
		$region    	 = 'us-east-1';		 
		$bucket		 = 'cinecliq.assets';		
				
		$fileTypes = array('jpg','jpeg','png','PNG','JPG','JPEG','svg','SVG'); // File extensions
		$size = 500*1024;
		
		$s3path = $this->request->file('Filedata');
		$s3name = $s3path->getClientOriginalName();
		$s3mimeType = $s3path->getClientOriginalExtension();
		$s3fileSize = $s3path->getClientSize();

		list($_width, $_height) = @getimagesize($this->request->file('Filedata'));
		
		if(in_array($s3mimeType, $fileTypes)){
			if($s3fileSize <= $size){
				if($_width/$_height == 2/3){
					if($_width <= 800 && $_width >= 400){
						if($_height <= 1200 && $_height >= 600){
							
							$s3 = Aws::factory([
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
							
							LocaleFilms::Where('films_id', $this->request->filmID)->where('locale', $locale)->update(array(
								'cover' => $s3name,
							));
							
							return [
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
	 * Remove Poster Image
	 * @POST("titles/metadata/castAndCrew/posterImageRemove")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return array
	 */
	public function posterImageRemove()
	{
		$localeID = (!empty($this->request->Input('localeID')) && is_numeric($this->request->Input('localeID'))) ? CHhelper::filterInput($this->request->Input('localeID')) : false;

		return LocaleFilms::Where('id', $localeID)->update([
			'cover' => '',
		]);
	}

	/**
	 * Upload TSplash Image
	 * @POST("titles/metadata/castAndCrew/tsplashImageUpload")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return array
	 */
    public function tsplashImageUpload()
    {
		$s3AccessKey = 'AKIAJPIY5AB3KDVIDPOQ';
		$s3SecretKey = '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';
		$region    	 = 'us-east-1';		 
		$bucket		 = 'cinecliq.assets';		
				
		$fileTypes = array('jpg','jpeg','png','PNG','JPG','JPEG','svg','SVG'); // File extensions
		$size = 500*1024;
		$maxWidth = 1920;
		$maxHeight = 1080;
		
		$s3path = $this->request->file('Filedata');
		$s3name = $s3path->getClientOriginalName();
		$s3mimeType = $s3path->getClientOriginalExtension();
		$s3fileSize = $s3path->getClientSize();
		

		list($_width, $_height, $_type) = @getimagesize($this->request->file('Filedata'));
		
		if(in_array($s3mimeType, $fileTypes)){
			if($s3fileSize <= $size){					
				if($_width <= $maxWidth){
					if($_height <= $maxHeight){
						
						$s3 = AWS::factory([
							'key'    => $s3AccessKey,
							'secret' => $s3SecretKey,
							'region' => $region,
						])->get('s3');	

						$s3->putObject([
							'Bucket' => $bucket,
							'Key'    => 'splash/'.$s3name,
							'Body'   => fopen($s3path, 'r'),			
							'SourceFile' => $s3path,
							'ACL'    => 'public-read',
						]);	
						
						Film::Where('id', $this->request->filmID)->update(array(
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
	 * Remove TSplash Image
	 * @POST("titles/metadata/castAndCrew/tsplashImageRemove")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return array
	 */
    public function tsplashImageRemove()
	{
		return Film::Where('id', $this->request->filmID)->update([
			'tsplash' => '',
		]);
	}	

	/**
	 * Upload FSplash Image
	 * @POST("titles/metadata/castAndCrew/fsplashImageUpload")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return array
	 */
    public function fsplashImageUpload()
    {
		$s3AccessKey = 'AKIAJPIY5AB3KDVIDPOQ';
		$s3SecretKey = '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';
		$region    	 = 'us-east-1';		 
		$bucket		 = 'cinecliq.assets';		
				
		$fileTypes = array('jpg','jpeg','png','PNG','JPG','JPEG','svg','SVG'); // File extensions
		$size = 500*1024;
		$maxWidth = 1920;
		$maxHeight = 1080;
		
		$s3path = $this->request->file('Filedata');
		$s3name = $s3path->getClientOriginalName();
		$s3mimeType = $s3path->getClientOriginalExtension();
		$s3fileSize = $s3path->getClientSize();
		

		list($_width, $_height) = @getimagesize($this->request->file('Filedata'));
		
		if(in_array($s3mimeType, $fileTypes)){
			if($s3fileSize <= $size){
				if($_width <= $maxWidth){
					if($_height <= $maxHeight){
						
						$s3 = AWS::factory([
							'key'    => $s3AccessKey,
							'secret' => $s3SecretKey,
							'region' => $region,
						])->get('s3');	

						$s3->putObject([
							'Bucket' => $bucket,
							'Key'    => 'splash/'.$s3name,
							'Body'   => fopen($s3path, 'r'),			
							'SourceFile' => $s3path,
							'ACL'    => 'public-read',
						]);	
						
						Film::Where('id', $this->request->filmID)->update(array(
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
	 * Remove FSplash Image
	 * @POST("titles/metadata/castAndCrew/fsplashImageRemove")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return array
	 */
    public function fsplashImageRemove()
	{
		return Film::Where('id', $this->request->filmID)->update(array(
			'fsplash' => '',
		));
	}

	/**
	 * Get Tab Subtitles
	 * @return array
	 */
    private function getSubtitles()
    {
		$filmSubtitles = $this->getFilmSubtitles();
		$trailerSubtitles = $this->getTrailerSubtitles();
		
		return compact('filmSubtitles', 'trailerSubtitles');
    }

	/**
	 * Get Film Subtitles
	 * @return collaction
	 */
    private function getFilmSubtitles()
    {
        return FilmSubtitles::where('films_id', $this->request->filmID)->where('deleted', '0')->orderBy('id', 'desc')->get();
    }

	/**
	 * Get Trailer Subtitles
	 * @return collaction
	 */
    private function getTrailerSubtitles()
    {
		return TrailerSubtitles::where('films_id', $this->request->filmID)->where('deleted', '0')->orderBy('id', 'desc')->get();
    }

	/**
	 * Save Subtitles Changes
	 * @POST("titles/metadata/subtitles/subtitlesSaveChanges")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return Response
	 */
    public function subtitlesSaveChanges()
    {
		$subtitleNames = (!empty($this->request->Input('subtitleNames')) && is_array($this->request->Input('subtitleNames'))) ? $this->request->Input('subtitleNames') : false;
		$tSubtitleNames = (!empty($this->request->Input('tSubtitleNames')) && is_array($this->request->Input('tSubtitleNames'))) ? $this->request->Input('tSubtitleNames') : false;

		if($subtitleNames) {
			foreach ($subtitleNames as $key => $value) {
				$filmSubtitleID = CHhelper::filterInputInt($key);
				$file = CHhelper::filterInput($this->request->Input('fsubtitleFile_' . $key));
				$title = CHhelper::filterInput($value);

				FilmSubtitles::where('id', $filmSubtitleID)->update([
					'title' => $title,
					'file' => $this->request->filmID . '/' . 'f/' . $file,
				]);
			}
		}
		if($tSubtitleNames){
			foreach($tSubtitleNames as $key => $value){
				$trailerSubtitleID = CHhelper::filterInputInt($key);
				$file = CHhelper::filterInput($this->request->Input('tsubtitleFile_'.$key));
				$title = CHhelper::filterInput($value);

				TrailerSubtitles::where('id', $trailerSubtitleID)->update([
					'title' => $title,
					'file' => $this->request->filmID.'/'.'t/'.$file,
				]);
			}
		}
    }

	/**
	 * Create New Film Subtitle
	 * @POST("titles/metadata/subtitles/CreateNewFilmSubtitle")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return Response Html
	 */
    public function CreateNewFilmSubtitle()
    {
		$filmSubTitle = CHhelper::filterInput($this->request->Input('filmSubTitle'));
		$file = CHhelper::filterInput($this->request->Input('fsubtitleFile'));
		
		FilmSubtitles::create([
			'title'    =>  $filmSubTitle,
			'file'     =>  $this->request->filmID.'/'.'f/'.$file,
			'films_id' =>  $this->request->filmID,
		]);


		$metadata = ['subtitles' => $this->getSubtitles()];
		return view('titles.titleManagement.metadata.partials.subtitles.subtitles', compact('metadata'));
    }

	/**
	 * Remove Film Subtitle
	 * @POST("titles/metadata/subtitles/removeFilmSubtitle")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return Response Html
	 */
    public function removeFilmSubtitle()
    {
		$subTitleID = (!empty($this->request->Input('subTitleID')) && is_numeric($this->request->Input('subTitleID'))) ? CHhelper::filterInputInt($this->request->Input('subTitleID')) : false;

		FilmSubtitles::where('id', $subTitleID)->update([
			'deleted' => '1',
		]);

		$metadata = ['subtitles' => $this->getSubtitles()];
		return view('titles.titleManagement.metadata.partials.subtitles.subtitles', compact('metadata'));
    }    

	/**
	 *@POST("titles/metadata/subtitles/CreateNewTrailerSubtitle")
	 * @Middleware("auth")
	*/

	/**
	 * Create New Trailer Subtitle
	 * @POST("titles/metadata/subtitles/CreateNewTrailerSubtitle")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return Response Html
	 */
    public function CreateNewTrailerSubtitle(Request $request)
    {
		$trailerSubTitle = CHhelper::filterInput($this->request->Input('trailerSubTitle'));
		$trailerFile = CHhelper::filterInput($this->request->Input('tsubtitleFile'));

		TrailerSubtitles::create([
			'title'    =>  $trailerSubTitle,
			'file'     =>  $this->request->filmID.'/'.'f/'.$trailerFile,
			'films_id' =>  $this->request->filmID,
		]);

		$metadata = ['subtitles' => $this->getSubtitles()];
		return view('titles.titleManagement.metadata.partials.subtitles.subtitles', compact('metadata'));
    }

	/**
	 * Remove Trailer Subtitle
	 * @POST("titles/metadata/subtitles/removeTrailerSubtitle")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return Response Html
	 */
    public function removeTrailerSubtitle()
    {
		$trailerSubTitleID = (!empty($this->request->Input('trailerSubTitleID')) && is_numeric($this->request->Input('trailerSubTitleID'))) ? CHhelper::filterInputInt($this->request->Input('trailerSubTitleID')) : false;

		TrailerSubtitles::where('id', $trailerSubTitleID)->update([
			'deleted' => '1',
		]);

		$metadata = ['subtitles' => $this->getSubtitles()];
		return view('titles.titleManagement.metadata.partials.subtitles.subtitles', compact('metadata'));
    }

	/**
	 * Upload Subtitle Files
	 * @POST("titles/metadata/subtitles/uploadFile")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return array
	 */
    public function uploadFile()
    {
		$fileName = CHhelper::filterInput($this->request->Input('fileName'));
		
		$s3AccessKey = 'AKIAJPIY5AB3KDVIDPOQ';
		$s3SecretKey = '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';
		$region    	 = 'us-east-1';		 
		$bucket		 = 'cinecliq.assets';		
				
		$fileTypes = array('srt','SRT'); // File extensions
		
		$s3path = $this->request->file('Filedata');
		$s3name = $s3path->getClientOriginalName(); // File Name
		$s3mimeType = $s3path->getClientOriginalExtension(); // File Mime Type
		
		if(in_array($s3mimeType, $fileTypes)){		
			$s3 = AWS::factory([
				'key'    => $s3AccessKey,
				'secret' => $s3SecretKey,
				'region' => $region,
			])->get('s3');	

			$s3->putObject([
				'Bucket' => $bucket,
				'Key'    => 'subtitles/'.$this->request->filmID.'/'.$fileName.'/'.$s3name,
				'Body'   => fopen($s3path, 'r'),			
				'SourceFile' => $s3path,
				'ACL'    => 'public-read',
			]);	
			
			return [
				'error' => 0,
				'fileName' => $s3name,
				'message' => 'File was uploaded successfully!'
			];			
		}else
			$response = [
				'error' => 1,
				'message' => $s3mimeType.' is invalid file type'
			];
				
		return $response;
    }

	/**
	 * Get Tab Age Ratings
	 * @return array
	 */
	private function getAgeRates()
    {
		$ageRates = AgeRates::join('cc_countries', 'cc_age_rates.countries_id', '=', 'cc_countries.id')
						->select(array('cc_age_rates.*', 'cc_countries.title as countryTitle', 'cc_countries.id as countryId'))
						->where('cc_age_rates.deleted', '<>', '1')
						->where('cc_countries.deleted', '<>', '1')
						->orderBy('countryTitle', 'asc')
						->get();
		$ageRate=array();
		
		foreach($ageRates as $key => $value)
			$ageRate[$value->countryTitle][] = $value;

		$filmRates = FilmsAgeRates::where('films_id', $this->request->filmID)->lists('age_rates_id', 'age_rates_id')->toArray();
		return compact('ageRate', 'filmRates');
    }

	/**
	 * Save Age Ratings Changes
	 * @POST("titles/metadata/subtitles/ageRateSaveChanges")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return Response
	 */
	public function ageRateSaveChanges()
	{
		$ageRatesArray = (!empty($this->request->input('ageRates')) && is_array($this->request->input('ageRates'))) ? $this->request->input('ageRates') : false;
		//dd($ageRatesArray);
		foreach($ageRatesArray as  $ageRateID) {
			$ageRateID = CHhelper::filterInputInt($ageRateID);
			if(!empty($ageRateID))
				FilmsAgeRates::create([
					'films_id' => $this->request->filmID ,
					'age_rates_id' => $ageRateID,
				]);
		}
	}

	/**
	 * Get Tab Age Ratings
	 * @return array
	 */
    private function getSeries()
    {
		$film = $this->request->film;
		$parentFilm = Film::where('deleted', '0')->find($film->series_parent);
		return compact('parentFilm');
    }

	/**
	 * Save Series Changes
	 * @POST("titles/metadata/seriesSaveChanges")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return Response
	 */
    public function seriesSaveChanges()
    {
		$seriesParent = CHhelper::filterInputInt($this->request->Input('series_parent'));
		$filmType = CHhelper::filterInput($this->request->Input('filmType'));
		
		if($filmType == -2){
			if(!empty($seriesParent)) {
				$seriesNum = CHhelper::filterInputInt($this->request->Input('series_num'));
				$filmType = $seriesParent;
			}else{
				$seriesNum = 0;
				$filmType = CHhelper::filterInput($this->request->Input('filmType'));
			}			
		}else{
			$seriesNum = 0;
		}
		
		return Film::where('id', $this->request->filmID)->update([
			'series_parent' => $filmType,
			'series_num' => $seriesNum,
		]);
    }

	/**
	 * Get Token Series
	 * @POST("titles/metadata/series/getTokenSeries")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return Response
	 */
	public function getTokenSeries()
	{
		$inputToken = (!empty($this->request->Input('inputToken'))) ? CHhelper::filterInput($this->request->Input('inputToken')) : false;

		if( $this->storeID > 0 && $this->companyID > 0)
		{
			$union = Film::distinct()->join('fk_films_owners', 'fk_films_owners.films_id', '=', 'cc_films.id')
				->where('fk_films_owners.owner_id', $this->companyID)
				->where('fk_films_owners.type', 0)
				->where('cc_films.deleted', 0)
				->where('cc_films.series_parent', '-1')
				->where('cc_films.title', 'like', $inputToken.'%');

			return Film::distinct()->join('cc_base_contracts', 'cc_base_contracts.films_id', '=', 'cc_films.id')
				->join('cc_channels_contracts', 'cc_channels_contracts.bcontracts_id', '=', 'cc_base_contracts.id')
				->where('cc_channels_contracts.channel_id', $this->storeID)
				->where('cc_films.deleted', 0)
				->where('cc_films.series_parent', '-1')
				->where('cc_films.title', 'like', $inputToken.'%')
				->union($union->select('cc_films.*'))
				->select('cc_films.*')->get();
		}
		elseif( $this->storeID > 0)
		{
			return Film::join('cc_base_contracts', 'cc_base_contracts.films_id', '=', 'cc_films.id')
				->join('cc_channels_contracts', 'cc_channels_contracts.bcontracts_id', '=', 'cc_base_contracts.id')
				->where('cc_channels_contracts.channel_id', $this->storeID)
				->where('cc_films.deleted', 0)
				->where('cc_films.series_parent', '-1')
				->where('cc_films.title', 'like', $inputToken.'%')
				->select('cc_films.*')->get();


		}
		elseif( $this->companyID > 0)
		{
			return Film::join('fk_films_owners', 'fk_films_owners.films_id', '=', 'cc_films.id')
				->where('fk_films_owners.owner_id', $this->companyID)
				->where('fk_films_owners.type', 0)
				->where('cc_films.deleted', 0)
				->where('cc_films.series_parent', '-1')
				->where('cc_films.title', 'like', $inputToken.'%')
				->select('cc_films.*')->get();
		}
	}

	/**
	 * Get Tab Seo
	 * @return array
	 */
	private function getSeo()
    {
		$keywords = ChannelsFilmsKeywords::where('films_id', $this->request->filmID)->get()->keyBy('locale');
		$allLocales = $this->getAllLocale();
		$seoAllUniqueLocales = CHhelper::getUniqueLocale($allLocales, $keywords);	
		
		return compact('keywords', 'seoAllUniqueLocales');
    }

	/**
	 * Show New Seo Item Modal Form
	 * @POST("titles/metadata/seo/showNewSeoItemForm")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return Response
	 */
	public function showNewSeoItemForm()
	{
		$keywords = ChannelsFilmsKeywords::where('films_id', $this->request->filmID)->get()->keyBy('locale');
		$allLocales = $this->getAllLocale();
		$metadata['seo']['seoAllUniqueLocales'] = CHhelper::getUniqueLocale($allLocales, $keywords);

		return view('titles.titleManagement.metadata.partials.seo.forms.addKeywords', compact('metadata'))->render();
	}

	/**
	 * Show Edit Seo Item Modal Form
	 * @POST("titles/metadata/seo/showEditSeoItemForm")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return Response
	 */
	public function showEditSeoItemForm()
	{
		$keywordID = (!empty($this->request->Input('keywordID')) && is_numeric($this->request->Input('keywordID'))) ?CHhelper::filterInputInt($this->request->Input('keywordID')) : false;
		$keywords = ChannelsFilmsKeywords::where('id', $keywordID)->where('films_id', $this->request->filmID)->get()->first();
		$allLocales = $this->getAllLocale();

		return view('titles.titleManagement.metadata.partials.seo.forms.editKeywords', compact('keywords', 'allLocales'))->render();
	}

	/**
	 * Add New Seo Item
	 * @POST("titles/metadata/seo/addSeoItem")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return Response
	 */
    public function addSeoItem()
    {
		ChannelsFilmsKeywords::create([
			'channels_id' => $this->storeID,
			'films_id' => $this->request->filmID,
			'description' => CHhelper::filterInput($this->request->Input('description')),
			'keywords' => CHhelper::filterInput($this->request->Input('keywords')),
			'locale' => CHhelper::filterInput($this->request->Input('countries')),
		]);

		$allLocales = $this->getAllLocale();
		$metadata['seo'] = $this->getSeo();
		return view('titles.titleManagement.metadata.partials.seo.list', compact('metadata', 'allLocales'))->render();
    }

	/**
	 * Edit Seo Item
	 * @POST("titles/metadata/seo/editSeoItem")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return Response
	 */
    public function editSeoItem()
    {
		$keywordID = (!empty($this->request->Input('keywordID')) && is_numeric($this->request->Input('keywordID'))) ?CHhelper::filterInputInt($this->request->Input('keywordID')) : false;

		if($keywordID) {
			ChannelsFilmsKeywords::where('id', $keywordID)->update([
				'description' => CHhelper::filterInput($this->request->Input('description')),
				'keywords' => CHhelper::filterInput($this->request->Input('keywords')),
				'locale' => CHhelper::filterInput($this->request->Input('countries')),
			]);
		}
		$allLocales = $this->getAllLocale();
		$metadata['seo'] = $this->getSeo();
		return view('titles.titleManagement.metadata.partials.seo.list', compact('metadata', 'allLocales'))->render();
    }

	/**
	 * Remove Seo Item
	 * @POST("titles/metadata/seo/removeSeoItem")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return Response
	 */
    public function removeSeoItem()
    {
		$keywordID = (!empty($this->request->Input('keywordID')) && is_numeric($this->request->Input('keywordID'))) ?CHhelper::filterInputInt($this->request->Input('keywordID')) : false;
		if($keywordID)
			 ChannelsFilmsKeywords::destroy($keywordID);

		$allLocales = $this->getAllLocale();
		$metadata['seo'] = $this->getSeo();
		return view('titles.titleManagement.metadata.partials.seo.list', compact('metadata', 'allLocales'))->render();
    }

	/**
	 * Publich And Unpublish Film
	 * @POST("titles/metadata/publishUnpublish")
	 * @Middleware("auth")
	 * @Middleware("filmPermission")
	 * @return Response
	 */
	public function publishUnpublish()
	{
		$status = (!empty($this->request->Input('filmStatus')) && is_numeric($this->request->Input('filmStatus'))) ? $this->request->Input('filmStatus') : false;

		if($status == 0 || $status == 1) {
			$baseContractID = $this->request->film->baseContract->id;

			return ChannelsContracts::where('bcontracts_id', $baseContractID)->where('channel_id', $this->storeID)->update([
				'film_status' => $status
			]);
		}
	}
}

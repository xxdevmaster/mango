<?php

namespace App\Http\Controllers\TitleMenegment;

use Auth;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\LocaleFilms;
use App\Film;
use App\Alllocales;
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
use App\Models\ChannelsFilmsKeywords;
use Illuminate\Support\Debug\Dumper;

use DB;
use Aws\Common\Aws;
use Intervention\Image\Facades\Image;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Http\Annotations\AnnotationsServiceProvider;


class MetadataController extends Controller
{
	protected $scanRoutes;
	
	private $id;
	
	private $template;
	
    public function metadataShow($id)
    {
		$current_menu = 'allTitles';
		 
		$film = $this->getFilm($id);
		$basic = $this->getBasic($id);
		$advanced = $this->getAdvanced($id);
		$castAndCrew = $this->getCastAndCrew($id);
		$images = $this->getImages($id);
		
		$ageRates = $this->getAgeRates($id);
		$series = $this->getSeries($id);
		$seo = $this->getSeo($id);
		
		$allLocales = $this->getAllLocale();
		
        return view('titles.titleMenegment.metadata.metadata', compact('current_menu', 'film', 'allLocales', 'advanced', 'castAndCrew', 'images', 'ageRates', 'series', 'seo'), $basic);
    }
	
	public function getFilm($id)
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

		if(count($film) != 0)
			return $film;
		else
			return 0;
	}

	public function getAllLocale()
	{
		$allLocale = Alllocales::select('title', 'code')->get()->toArray();
		
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
		$template = array();
		
		$this->id = trim(filter_var($request->Input('filmId'),FILTER_SANITIZE_NUMBER_INT));
		$this->template = trim(filter_var($request->Input('template'),FILTER_SANITIZE_STRING));
		
		$film = $this->getFilm($this->id);
		$allLocales = $this->getAllLocale();	
		
		if($this->template === 'basic')
			$template = $this->getBasic($this->id);
		elseif($this->template === 'castAndCrew')
			$castAndCrew = $this->getCastAndCrew($this->id);
		else
			$castAndCrew = '';		
		
		return view('titles.titleMenegment.metadata.partials.'.$this->template.'.'.$this->template, compact('film', 'allLocales', 'castAndCrew'),  $template);
	} 	
	
	/**
	 *@POST("/titles/metadata/getTemplate")
	 * @Middleware("auth")
	*/
	public function _getTemplate(Request $request)
	{
		
		$this->id = trim(filter_var($request->Input('filmId'),FILTER_SANITIZE_NUMBER_INT));
		$this->template = $request->Input('template');// trim(filter_var($request->Input('template'),FILTER_SANITIZE_STRING));
		
		return view('titles.titleMenegment.metadata.partials.'.$this->template.'.'.$this->template,$this->getBasic($this->id));
	} 

    public function getBasic($id)
    {		
		$filmLocales = LocaleFilms::where('films_id', $this->id)->where('deleted', 0)->orderBy('def', 'desc')->orderBy('id', 'asc')->get();
						
		return compact('filmLocales');
		
    } 
	
	/**
	 *@POST("titles/metadata/basicSaveChanges")
	 * @Middleware("auth")
	*/
    public function basicSaveChanges(Request $request)
    {
		foreach($request->Input('filmsLocales') as $filmsLocales => $filmsLocalesValue) {
			
			$localeUpdate =  LocaleFilms::where('id', $filmsLocalesValue['localeId'])->update(array(
				'title' => $filmsLocalesValue['title'],
				'synopsis' => $filmsLocalesValue['synopsis']
			));	
			
			if($filmsLocalesValue['def'] == '1'){
				
				$filmLocaleUpdate =  Film::where('id', $request->Input('filmId'))->update(array(
					'title' => $filmsLocalesValue['title'],
					'synopsis' => $filmsLocalesValue['synopsis'],
				));	
				
			}
		}
		
		return 1;
    } 

	/**
	 *@POST("titles/metadata/basic/newLocale")
	 * @Middleware("auth")
	*/
	public function basicAddNewLocale(Request $request)
    {
		$filmId=trim(filter_var($request->Input('filmId'),FILTER_SANITIZE_NUMBER_INT));
		$locale=trim(filter_var($request->Input('locale'),FILTER_SANITIZE_STRING));// cc_films.i18n
        $newLocaleId = LocaleFilms::create([
			'films_id' => $filmId,
			'locale' => $locale,
		])->id;
		
		if($newLocaleId > 0)
			return $newLocaleId;
		else
			return 0;
		
    }
	
	/**
	 *@POST("titles/metadata/basic/localeRemove")
	 * @Middleware("auth")
	*/
    public function basicLocaleRemove(Request $request)
    {
		$localeId=trim(filter_var($request->Input('localeId'),FILTER_SANITIZE_NUMBER_INT));

		$localeRemove =  LocaleFilms::where('id', $localeId)->where('def', '<>', '1')->update(array(
			'deleted' => 1
		));	
		
        if($localeRemove)
			return 1;
		else
			return 0;
		
    }   

	/**
	 *@POST("titles/metadata/basic/makeDefaultLocale")
	 * @Middleware("auth")
	*/
	public function makeDefaultLocale(Request $request)
    {
		$locale=trim(filter_var($request->Input('locale'),FILTER_SANITIZE_STRING));
		$localeId=trim(filter_var($request->Input('localeId'),FILTER_SANITIZE_NUMBER_INT));
		$filmId=trim(filter_var($request->Input('filmId'),FILTER_SANITIZE_NUMBER_INT));
		
		$localeMakeDefault =  LocaleFilms::where('films_id', $filmId)->where('def', '1')->update(array(
			'def' => 0
		));
		$localeMakeDefault =  LocaleFilms::where('id', $localeId)->update(array(
			'def' => 1
		));
		
		$localeMakeDefault =  Film::where('id', $filmId)->update(array(
			'i18n' => $locale,
			'locale' => $locale
		));
        return 1;
    }	
	
	public function getAdvanced($id)	
	{
		$film = $this->getFilm($id);
		$filmGenres = $film->genres()->get()->keyBy('id');
		$filmLanguages = $film->languages()->get()->keyBy('id');
		$filmProdCompanies = $film->prodCompanies()->get()->keyBy('id');
		$filmCountries = $film->countries()->get()->keyBy('id');

		return compact('filmGenres', 'filmLanguages', 'filmProdCompanies', 'filmCountries');
	}

	/**
	 *@POST("titles/metadata/advanced/getTokenGenres")
	 * @Middleware("auth")
	*/	
	public function getTokenGenres(Request $request)
	{
		$inputToken = trim(filter_var($request->Input('inputToken'),FILTER_SANITIZE_STRING));
		$genre = Genres::where('deleted', '0')->where('title', 'like', $inputToken.'%')->get();
		return $genre;
	}	

	/**
	 *@POST("titles/metadata/advanced/getTokenOriginalLanguages")
	 * @Middleware("auth")
	*/	
	public function getTokenOriginalLanguages(Request $request)
	{
		$inputToken = trim(filter_var($request->Input('inputToken'),FILTER_SANITIZE_STRING));
		$originalLanguage = Languages::where('deleted', '0')->where('title', 'like', $inputToken.'%')->get();
		return $originalLanguage;
	}	
	
	/**
	 *@POST("titles/metadata/advanced/getTokenProdCompanies")
	 * @Middleware("auth")
	*/	
	public function getTokenProdCompanies(Request $request)
	{
		$inputToken = trim(filter_var($request->Input('inputToken'),FILTER_SANITIZE_STRING));
		$prodCompanies = ProdCompanies::where('deleted', '0')->where('title', 'like', $inputToken.'%')->get();
		return $prodCompanies;
	}	
	
	/**
	 *@POST("titles/metadata/advanced/getTokenCountries")
	 * @Middleware("auth")
	*/	
	public function getTokenCountries(Request $request)
	{
		$inputToken = trim(filter_var($request->Input('inputToken'),FILTER_SANITIZE_STRING));
		$countries = Countries::where('deleted', '0')->where('title', 'like', $inputToken.'%')->get();
		return $countries;
	}

	/**
	 *@POST("titles/metadata/advancedSaveChanges")
	 * @Middleware("auth")
	*/	
	public function advancedSaveChanges(Request $request)
	{
		$filmId = trim(filter_var($request->Input('filmId'),FILTER_SANITIZE_NUMBER_INT));
		$dt = trim(filter_var($request->Input('dt'),FILTER_SANITIZE_STRING));
		$duration = trim(filter_var($request->Input('duration'),FILTER_SANITIZE_STRING));
		$admComment = trim(filter_var($request->Input('admcomment'),FILTER_SANITIZE_STRING));
		
		FilmsGenres::destroy(['films_id', $filmId]);
		foreach($request->Input('genres') as $genresId => $val) {
			$fkFilmsGenres = FilmsGenres::create([
				'films_id' => $filmId,
				'genres_id' => $genresId,
			]);
		}
		
		FilmsLanguages::destroy(['films_id', $filmId]);
		foreach($request->Input('originalLanguages') as $originalLanguagesId => $val) {
			$fkFilmsLanguages = FilmsLanguages::create([
				'films_id' => $filmId,
				'languages_id' => $originalLanguagesId,
			]);
		}		
		
		FilmsProdCompanies::destroy(['films_id', $filmId]);
		//if(is_array($request->Input('productCompanies')))
		foreach($request->Input('productCompanies') as $productCompaniesId => $val) {
			$fkFilmsProductCompanies = FilmsProdCompanies::create([
				'films_id' => $filmId,
				'prodcompanies_id' => $productCompaniesId,
			]);
		}		
		
		FilmsCountries::destroy(['films_id', $filmId]);
		foreach($request->Input('countries') as $countriesId => $val) {
			$fkFilmsProductCountries = FilmsCountries::create([
				'films_id' => $filmId,
				'countries_id' => $countriesId,
			]);
		}
		
		$fkFilmsProductCountries = Film::where('id', $filmId)->update([
			'id' => $filmId,
			'dt' => $dt,
			'duration' => $duration,
			'admcomment' => $admComment,
		]);
			
		return 1;		
		
	}

	public function getCastAndCrew($id)	
	{
		
		// DB::enableQueryLog();
		// $film = Film::where('deleted', '0')->find($id);
		
		$film = $this->getFilm($id);
		$person = $film->persons()->get();
		//$job = $film->jobs()->get();

       // (new Dumper)->dump($queries);
		//dd($person->first());
		
		return compact('person');
	}

	/**
	 *@POST("titles/metadata/castAndCrew/personCreate")
	 * @Middleware("auth")
	*/		
	public function personCreate(Request $request)
    {
		$filmId=trim(filter_var($request->Input('filmId'),FILTER_SANITIZE_NUMBER_INT));
        $personTitle = trim(filter_var($request->Input('persons'),FILTER_SANITIZE_STRING));
        $JobId = trim(filter_var($request->Input('jobs'),FILTER_SANITIZE_NUMBER_INT));
		
		if(empty($personTitle) || empty($JobId))
			return 0;
		$newPersonId = Persons::create([
			'title' => $personTitle 
		])->id;
		
		if($newPersonId){
			return FilmsPersons::create([
				'films_id' => $filmId,
				'persons_id' => $newPersonId,
				'jobs_id' => $JobId
			]);			
		}
		
		return 0;
    }

	/**
	 *@POST("titles/metadata/castAndCrew/getTokenPerson")
	 * @Middleware("auth")
	*/	
	public function getTokenPerson(Request $request)
	{
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
		$inputToken = trim(filter_var($request->Input('inputToken'),FILTER_SANITIZE_STRING));
		$genre = Jobs::where('deleted', '0')->where('title', 'like', $inputToken.'%')->get()->take(20);
		return $genre;
	}
	
	/**
	 *@POST("titles/metadata/castAndCrew/getPersonEditForm")
	 * @Middleware("auth")
	*/		
	public function getPersonEditForm(Request $request)
	{
		$personId=trim(filter_var($request->Input('personId'),FILTER_SANITIZE_NUMBER_INT));
		
		$thisPerson = Persons::where('id', $personId)->where('deleted', '0')->get();
		$LocalePersons = LocalePersons::where('persons_id', $personId)->get();
		
		$allLocales = $this->getAllLocale();
		
		return view('titles.titleMenegment.metadata.partials.castAndCrew.forms.editPersonForm', compact('thisPerson', 'LocalePersons', 'allLocales'));
	}

	/**
	 *@POST("titles/metadata/castAndCrew/personAddNewLocale")
	 * @Middleware("auth")
	*/		
	public function personAddNewLocale(Request $request)
	{
		$personId=trim(filter_var($request->Input('personId'),FILTER_SANITIZE_NUMBER_INT));
		$locale=trim(filter_var($request->Input('locale'),FILTER_SANITIZE_STRING));

        return LocalePersons::create([
			'persons_id' => $personId,
			'locale' => $locale,
		])->id;				
	}	
	
	/**
	 *@POST("titles/metadata/castAndCrew/removePersonLocale")
	 * @Middleware("auth")
	*/		
	public function removePersonLocale(Request $request)
	{
		$localeId=trim(filter_var($request->Input('localeId'),FILTER_SANITIZE_NUMBER_INT));

        return LocalePersons::destroy($localeId);				
	}	
	
	/**
	 *@POST("titles/metadata/castAndCrew/personRemove")
	 * @Middleware("auth")
	*/	
    public function personRemove(Request $request)
    {
        $personId=trim(filter_var($request->Input('personId'),FILTER_SANITIZE_NUMBER_INT));
		
		$personRemove = Persons::where('id', $personId)->update([
			'deleted' => '1'
		]);
		
		if($personRemove){
			LocalePersons::where('persons_id', $personId)->delete();
			return 1;
		}
			
		return 0;
    }

	/**
	 *@POST("titles/metadata/castAndCrew/personEdit")
	 * @Middleware("auth")
	*/	
    public function personEdit(Request $request)
    {
		$personId=trim(filter_var($request->Input('personId'),FILTER_SANITIZE_NUMBER_INT));
		$title=trim(filter_var($request->Input('title'),FILTER_SANITIZE_STRING));
		$brief=trim(filter_var($request->Input('brief'),FILTER_SANITIZE_STRING));
		
		if(!empty($request->Input('persons'))){
			foreach($request->Input('persons') as $key => $val) {
				$localeUpdate =  LocalePersons::where('id', $val['localeId'])->update(array(
					'title' => $val['title'],
					'brief' => $val['brief']
				));		
			}			
		}
		
		return Persons::where('id', $personId)->where('deleted', '0')->update(array(
			'title' => $title,
			'brief' => $brief
		));	
		
    }

	/**
	 *@POST("titles/metadata/castAndCrew/personImageUpload")
	 * @Middleware("auth")
	*/	
    public function personImageUpload(Request $request)
    {
		$personId=trim(filter_var($request->Input('personId'),FILTER_SANITIZE_NUMBER_INT));
		
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
						
						Persons::Where('id', $personId)->update([
							'img' => $s3name,
						]);
						
						return  [
									'error' => 0,
									'message' => $s3name
								];
					}else
						$response = [
							'error' => 1,
							'message' => 'Your image could not be uploaded as it does not have the correct aspect ration of 2:3'
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
	 *@POST("titles/metadata/castAndCrew/removePersonImage")
	 * @Middleware("auth")
	*/	
    public function removePersonImage(Request $request)
    {
		$personId=trim(filter_var($request->Input('personId'),FILTER_SANITIZE_NUMBER_INT));
		
		return Persons::where('id', $personId)->update([
			'img' => 'nophoto.png'
		]);
    }
	
    // public function posterImageUpload()
    // {
        
    // }
	
    // public function posterImageDestroy()
    // {
        
    // }
	
	public function getImages($id)
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
		$filmId=trim(filter_var($request->Input('filmId'),FILTER_SANITIZE_NUMBER_INT));
		$locale=trim(filter_var($request->Input('locale'),FILTER_SANITIZE_STRING));// cc_films.i18n
		
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
							'message' => 'Your image could not be uploaded as it does not have the correct aspect ration of 2:3'
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
		$localeId=trim(filter_var($request->Input('localeId'),FILTER_SANITIZE_NUMBER_INT));
		
		$removeCover = LocaleFilms::Where('id', $localeId)->update(array(
			'cover' => '',
		));	
		
		return $removeCover;
	}

	/**
	 *@POST("titles/metadata/castAndCrew/tsplashImageUpload")
	 * @Middleware("auth")
	*/		
    public function tsplashImageUpload(Request $request)
    {
		
		$filmId=trim(filter_var($request->Input('filmId'),FILTER_SANITIZE_NUMBER_INT));
		$locale=trim(filter_var($request->Input('locale'),FILTER_SANITIZE_STRING));// cc_films.i18n
		
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
				if($_width <= $width && $_width >= $width/2){
					if($_height <= $height && $_height >= $height/2){
						
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
							'message' => 'Your image could not be uploaded as it does not have the correct aspect ration of 2:3'
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
	 *@POST("titles/metadata/castAndCrew/tsplashImageRemove")
	 * @Middleware("auth")
	*/		
    public function tsplashImageRemove(Request $request)
	{
		$filmId=trim(filter_var($request->Input('filmId'),FILTER_SANITIZE_NUMBER_INT));
		
		$removeCover = Film::Where('id', $filmId)->update(array(
			'tsplash' => '',
		));	
		
		return $removeCover;		
	}	
	
	
	/**
	 *@POST("titles/metadata/castAndCrew/fsplashImageUpload")
	 * @Middleware("auth")
	*/		
    public function fsplashImageUpload(Request $request)
    {
		
		$filmId=trim(filter_var($request->Input('filmId'),FILTER_SANITIZE_NUMBER_INT));
		$locale=trim(filter_var($request->Input('locale'),FILTER_SANITIZE_STRING));// cc_films.i18n
		
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
				if($_width <= $width && $_width >= $width/2){
					if($_height <= $height && $_height >= $height/2){
						
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
							'message' => 'Your image could not be uploaded as it does not have the correct aspect ration of 2:3'
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
	 *@POST("titles/metadata/castAndCrew/fsplashImageRemove")
	 * @Middleware("auth")
	*/		
    public function fsplashImageRemove(Request $request)
	{
		$filmId=trim(filter_var($request->Input('filmId'),FILTER_SANITIZE_NUMBER_INT));
		
		$removeCover = Film::Where('id', $filmId)->update(array(
			'fsplash' => '',
		));	
		
		return $removeCover;		
	}
	
	
    public function filmSubtitleCreate()
    {
        //
    }
	
	
    public function filmSubtitleDestroy()
    {
        //
    }
	
    public function trailerSubtitleCreate()
    {
        //
    }
	
	
    public function trailerSubtitleDestroy()
    {
        //
    }    
	
	public function getAgeRates($id)
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
			$filmRates[] = $key->age_rates_id;
		}
		
		//dd($filmRates);
       // dd($ageRate);
		
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
		
		return compact('keywords');	
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
		$keywordsId=trim(filter_var($request->Input('keywordsId'),FILTER_SANITIZE_NUMBER_INT));
		
		$locale=trim(filter_var($request->Input('countries'),FILTER_SANITIZE_STRING));
		$keywords=trim(filter_var($request->Input('keywords'),FILTER_SANITIZE_STRING));
		$description=trim(filter_var($request->Input('description'),FILTER_SANITIZE_STRING));

		return ChannelsFilmsKeywords::where('id', $keywordsId)->update([
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

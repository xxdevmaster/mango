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
use App\Models\Jobs;
use App\Models\LocalePersons;
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
		$film = $this->getFilm($id);
		$basic = $this->getBasic($id);
		$advanced = $this->getAdvanced($id);
		$castAndCrew = $this->getCastAndCrew($id);
		$images = $this->getImages($id);
		
		$allLocales = $this->getAllLocale();
		
        return view('titles.titleMenegment.metadata.metadata', compact('film', 'advanced', 'castAndCrew', 'images', 'allLocales'), $basic);
    }
	
	public function getFilm($id)
	{
		return Film::where('deleted', '0')->find($id);
	}
	
    public function getBasic($id)
    {
		$this->id = (int) $id;
		
        $current_menu = 'allTitles';
		
        $userInfo = Auth::user();

        $accountInfo = $userInfo->account;

        $accountFeatures = $accountInfo->features;

        $companyInfo = $accountInfo->company;

        $companyFilms = $companyInfo->films()->where('cc_films.deleted', '0')->get();
		
		$film = $companyInfo->films()->where( 'cc_films.id', $this->id)->get();
		$currentFilm = $film->toArray();
		if(count($film->toArray()) === 0) {
			$storeInfo = $accountInfo->store;
			$storeFilms = $storeInfo->contracts()->with( 'films', 'stores' )->where( 'films_id', $this->id )->get();
			foreach($storeFilms as $key=>$store_film){
				$store_film->films->stores = $store_film->stores;
				$store_film->films->companies = $store_film->films->companies()->where('fk_films_owners.type', '0')->get();
				$storeFilms[$key] = $store_film->films;
				$currentFilm[] = $storeFilms[$key]->toArray();
			}
		}else {
			$currentFilm = $film->toArray();
		}

		$currenLanguages = LocaleFilms::where('films_id', $this->id)->where('deleted', 0)->where('def', '0')->get()->toArray();		
		$currenDefaultLanguages = LocaleFilms::where('films_id', $this->id)->where('deleted', 0)->where('def', '1')->get()->toArray();
						
		return compact('current_menu','currentFilm','currenLanguages', 'currenDefaultLanguages');
		
    } 
	
	/**
	 *@POST("/titles/metadata/basic/getTemplate")
	 * @Middleware("auth")
	*/
	public function getTemplate(Request $request){
		
		$this->id = trim(filter_var($request->Input('filmId'),FILTER_SANITIZE_NUMBER_INT));
		$this->template = $request->Input('template');// trim(filter_var($request->Input('template'),FILTER_SANITIZE_STRING));
		return view('titles.titleMenegment.metadata.partials.'.$this->template.'.'.$this->template, $this->getBasic($this->id), $this->getAdvanced($this->id), $this->getCastAndCrew($this->id));
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
		}
		$filmLocaleUpdate =  LocaleFilms::where('films_id', $request->Input('filmId'))->where('def', '1')->update(array(
			'title' => $request->Input('title'),
			'synopsis' => $request->Input('synopsis')
		));	
		$filmLocaleUpdate =  Film::where('id', $request->Input('filmId'))->update(array(
			'title' => $request->Input('title'),
			'synopsis' => $request->Input('synopsis')
		));	
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
		$localeId=trim(filter_var($request->Input('localeId'),FILTER_SANITIZE_STRING));

		$localeDelete =  LocaleFilms::where('id', $localeId)->where('def', '<>', '1')->update(array(
			'deleted' => 1
		));	
		
        if($localeDelete)
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
		
		DB::enableQueryLog();
		$film = Film::where('deleted', '0')->find($id);
		
		$person = $film->persons()->get();
		
		//$job = $film->jobs()->get();

       // (new Dumper)->dump($queries);
		//dd($person->first());
		
		return compact('person');
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

        $newPersonLocale = LocalePersons::create([
			'persons_id' => $personId,
			'locale' => $locale,
		])->id;
		
		if($newPersonLocale > 0)
			return 1;
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
	 *@POST("titles/metadata/castAndCrew/getTokenPerson")
	 * @Middleware("auth")
	*/	
	public function getTokenPerson(Request $request)
	{
		$inputToken = trim(filter_var($request->Input('inputToken'),FILTER_SANITIZE_STRING));
		$genre = Persons::where('deleted', '0')->where('title', 'like', $inputToken.'%')->get()->take(20);
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
	 *@POST("titles/metadata/castAndCrew/personRemove")
	 * @Middleware("auth")
	*/	
    public function personRemove(Request $request)
    {
        $personId=trim(filter_var($request->Input('personId'),FILTER_SANITIZE_NUMBER_INT));
		
		if(Persons::destroy($personId)) {
			return LocalePersons::where('persons_id', $personId)->delete();			
			return  1;
		}
		
		return 0;
    }

	/**
	 *@POST("titles/metadata/castAndCrew/personEdit")
	 * @Middleware("auth")
	*/	
    public function personEdit(Request $request)
    {
		return $request->all();
		$personId=trim(filter_var($request->Input('personId'),FILTER_SANITIZE_NUMBER_INT));
		
		foreach($request->Input('persons') as $key => $val) {
			$localeUpdate =  LocalePersons::where('id', $val['localeId'])->update(array(
				'title' => $val['title'],
				'brief' => $val['brief']
			));	
		}
		
		Persons::where('id', $request->Input('person_id', $personId))->where('deleted', '0')->update(array(
			'title' => $request->Input('title'),
			'brief' => $request->Input('brief')
		));	
		
		return 1;
    }
	
	// public function personCreate()
    // {
        
    // }

    // public function personImageUpload()
    // {
        
    // }

    // public function personImageDestroy()
    // {
        
    // }
	
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
		$s3AccessKey = 'AKIAJPIY5AB3KDVIDPOQ';
		$s3SecretKey = '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';
		$region    	 = 'us-east-1';		 
		$bucket		 = 'cinecliq.assets';		
				
		$fileTypes = array('jpg','jpeg','png','PNG','JPG','JPEG','svg','SVG'); // File extensions		
		list($_width, $_height, $_type, $_attr) = @getimagesize($request->file('Filedata'));

		
		$this->resize($request->file('Filedata'), 5000);
		
		// if(($_width/$_height) === (400/600) && $_width>400 && $_height>600){
			
		// }
		// $s3name = $request->file('Filedata')->getClientOriginalName();
		// $s3path = $request->file('Filedata');
		
		
		

		
		// $s3 = AWS::factory([
			// 'key'    => $access_key,
			// 'secret' => $secret_key,
			// 'region' => $region,
		// ])->get('s3');
		
		
	    // $response = $s3->putObject([
			// 'Bucket' => $bucket,
			// 'Key'    => $s3path,
			// 'Body'   => fopen($request->file('Filedata'), 'r'),			
			// 'SourceFile' => $s3path,
			// 'ACL'    => 'public-read',
		// ]);
		
		// dd($s3);
		
		//dd($response);
    }


	private function resize($image, $size)
    {
    	try 
    	{
    		$extension 		= 	$image->getClientOriginalExtension();
    		$imageRealPath 	= 	$image->getRealPath();
    		$thumbName 		= 	'thumb_'. $image->getClientOriginalName();
	    	
	    	//$imageManager = new ImageManager(); // use this if you don't want facade style code
    		//$img = $imageManager->make($imageRealPath);
	    
	    	$img = Image::make($imageRealPath); // use this if you want facade style code
	    	$img->resize(intval($size), null, function($constraint) {
	    		 $constraint->aspectRatio();
	    	});
	    	return $img->save(public_path('images'). '/'. $thumbName);
    	}
    	catch(Exception $e)
    	{
    		return false;
    	}

    }

	
    public function splashImageUpload()
    {
       
    }
	
	
    public function splashImageDestroy()
    {
        //
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
	
    public function keywordsAndDescriptionShow()
    {
        //
    }
	
	
    public function keywordsAndDescriptionCreate()
    {
        //
    }
	
	
    public function keywordsAndDescriptionUpdae()
    {
        //
    }
	
    public function keywordsAndDescriptionDestroy()
    {
        //
    }
}

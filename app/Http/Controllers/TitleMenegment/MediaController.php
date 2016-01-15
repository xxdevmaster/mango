<?php

namespace App\Http\Controllers\TitleMenegment;

use Guzzle\Http\Client;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use App\Libraries\CHhelper\CHhelper;

use App\Film;
use App\AllLocales;
use App\Models\FilmsMedia;
use App\Models\BitJobs;
use App\Libraries\CHuploader\amazoneAssetsBuilder;


class MediaController extends Controller
{
	private $filmId;

    private $film;

    private $request;

	public function __construct(Request $request)
	{
        $this->filmId = $request->filmId;
        $this->film = $request->film;
        $this->request = $request;
	}

    public function mediaShow()
    {
        $current_menu = 'Media';
        $film = $this->request->film;

        $allLocales = $this->getAllLocale();

        $media = [
            'storage' => $this->tabStorage(),
            'streaming' => $this->tabStreaming(),
            'movie' => $this->tabDubbedVersions('movie'),
            'trailer' => $this->tabDubbedVersions('trailer'),
            'extras' => $this->tabExtras(),
            'uploader' => $this->tabUploader(true),
            'uploadHistory' => $this->getUploaderHistory()
        ];

        return view('titles.titleMenegment.media.media', compact('current_menu', 'film', 'allLocales', 'media'));
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

    private function drawTemplate($template)
    {
        $film = $this->request->film;
        $allLocales = $this->getAllLocale();

        switch($template){
            case 'movie' :  $media = [
                                'movie' => $this->tabDubbedVersions('movie')
                            ];
                            $filmsMedia = $media['movie'];
                            return view('titles.titleMenegment.media.partials.dubbedVersions.partials.movieAndTrailer', compact('film', 'allLocales', 'filmsMedia'));break;
            case 'trailer' :  $media = [
                                'trailer' => $this->tabDubbedVersions('trailer')
                            ];
                            $filmsMedia = $media['trailer'];
                            return view('titles.titleMenegment.media.partials.dubbedVersions.partials.movieAndTrailer', compact('film', 'allLocales', 'filmsMedia'));break;
        }
    }

    private function tabStreaming()
    {
        $lastmonth = mktime(0, 0, 0, date("m")-1, date("d"),   date("Y"));
        $lastmonth = date("Y-m-d", $lastmonth);
        $dateNow = date("Y-m-d");

        return compact('lastmonth', 'dateNow');
    }

    /**
     *@POST("/titles/media/streaming/getStreaming")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function getStreaming()
    {
        if(!empty($this->request->input('cp')))
            $cp = '&provider_id='.CHhelper::filterInput($this->request->input('cp'));
    }

    private function tabStorage()
    {
        $userInfo = Auth::user();

        $accountInfo = $userInfo->account;
        $accountId = $accountInfo->id;

        $clientCdn = new Client();
        $urlCdn = $clientCdn->get('http://billing.cinehost.com/filmStorage/cdn?account_id='.$accountId.'&film_ids='.$this->filmId);
        $responseCdn = $urlCdn->send();
        $bodyCdn = $responseCdn->getBody(true);

        if(!$bodyCdn){
            return [
                'error' => '1',
                'message' => 'Curl Film Storage Error'
            ];
        }
        $outCdn = json_decode($bodyCdn);

        $clientMez = new Client();
        $urlMez = $clientMez->get('http://billing.cinehost.com/filmStorage/mezzanine?account_id='.$accountId.'&films_id='.$this->filmId);
        $responseMez = $urlMez->send();
        $bodyMez = $responseMez->getBody(true);

        if(!$bodyMez){
            return [
                'error' => '1',
                'message' => 'Curl Mezzanine Error'
            ];
        }
        $outMez = json_decode($bodyMez);

        $storageDataCdn = array();
        $storageDataMez = array();

        if(is_array($outCdn)){
            foreach($outCdn as $k => $v){
                if(isset($storageDataCdn[$v->type]))
                    $storageDataCdn[$v->type] = $storageDataCdn[$v->type] + $v->bytes;
                else
                    $storageDataCdn[$v->type] = $v->bytes;
                if(isset($storageDataMez[$v->type]))
                    $storageDataCdn['total'] = $storageDataCdn['total'] + $v->bytes;
                else
                    $storageDataCdn['total'] = $v->bytes;
            }
        }

        if(is_array($outMez)){
            foreach($outMez as $k => $v){
                if(isset($storageDataMez[$v->type]))
                    $storageDataMez[$v->type] = $storageDataMez[$v->type] + $v->bytes;
                else
                    $storageDataMez[$v->type] = $v->bytes;
                if(isset($storageDataMez['total']))
                    $storageDataMez['total'] = $storageDataMez['total'] + $v->bytes;
                else
                    $storageDataMez['total'] = $v->bytes;
            }
        }

        if(isset($storageDataCdn['film']))
            $featureCdn = ($storageDataCdn['film'] >0)? CHhelper::convertBytes($storageDataCdn['film']):'0 B';
        if(isset($storageDataCdn['trailer']))
            $trailerCdn = ($storageDataCdn['trailer'] >0)? CHhelper::convertBytes($storageDataCdn['trailer']):'0 B';
        if(isset($storageDataCdn['bonus']))
            $extraCdn = ($storageDataCdn['bonus'] >0)? CHhelper::convertBytes($storageDataCdn['bonus']):'0 B';
        if(isset($storageDataCdn['total']))
            $totalCdn = ($storageDataCdn['total'] >0)? CHhelper::convertBytes($storageDataCdn['total']):'0 B';

        if(isset($storageDataMez['film']))
            $featureMez = ($storageDataMez['film'] >0)? CHhelper::convertBytes($storageDataMez['film']):'0 B';
        if(isset($storageDataMez['trailer']))
            $trailerMez = ($storageDataMez['trailer'] >0)? CHhelper::convertBytes($storageDataMez['trailer']):'0 B';
        if(isset($storageDataMez['bonus']))
            $extraMez = ($storageDataMez['bonus'] >0)? CHhelper::convertBytes($storageDataMez['bonus']):'0 B';
        if(isset($storageDataMez['total']))
            $totalMez = ($storageDataMez['total'] >0)? CHhelper::convertBytes($storageDataMez['total']):'0 B';

        return compact('featureCdn', 'trailerCdn', 'extraCdn', 'totalCdn', 'featureMez', 'trailerMez', 'extraMez', 'totalMez');
    }

    private function tabDubbedVersions($type)
    {
        return FilmsMedia::where('films_id', $this->filmId)->where('type', $type)->where('deleted', '0')->get();
    }

    /**
     *@POST("/titles/media/dubbedVersions/dubbedVersionsCreate")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function dubbedVersionsCreate()
    {
        if(empty($this->request->Input('locale'))){
            return [
                'error' => '1' ,
                'message' => 'Film locale doesnt exixst'
            ];
        }

        if(!array_key_exists($this->request->Input('locale'), $this->getAllLocale())){
            return [
                'error' => '1' ,
                'message' => 'Invalid locale'
            ];
        }

        if(empty($this->request->Input('type')) || $this->request->Input('type') != 'movie' && $this->request->Input('type') != 'trailer'){
            return [
                'error' => '1' ,
                'message' => 'Dubbed version type doesnt exixst'
            ];
        }

        $locale = CHhelper::filterInput($this->request->Input('locale'));
        $type = CHhelper::filterInput($this->request->Input('type'));

        $createmovie = FilmsMedia::create([
            'films_id' => $this->filmId,
            'locale' => $locale,
            'type' => $type
        ])->id;

        if($createmovie > 0){
            $html = strval($this->drawTemplate($type));
            return [
                'error' => '0' ,
                'message' => 'Movie created seccessfully!',
                'html' => $html
            ];
        }else
            return [
              'error' => '1' ,
              'message' => 'Movie don`t created!'
            ];
    }

    /**
     *@POST("/titles/media/dubbedVersions/dubbedVersionsRemove")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function dubbedVersionsRemove()
    {
        if(empty($this->request->Input('movieId'))){
            return [
                'error' => '1' ,
                'message' => 'Movie identifier doesnt exixst'
            ];
        }
        if(!is_numeric($this->request->Input('movieId'))){
            return [
                'error' => '1' ,
                'message' => 'Identifier Movie not valid format'
            ];
        }

        $movieId = CHhelper::filterInputInt($this->request->Input('movieId'));
        $type = CHhelper::filterInput($this->request->Input('type'));

        $movieRemove =  FilmsMedia::where('id', $movieId)->where('films_id', $this->filmId)->update([
            'deleted' => '1'
        ]);

        if($movieRemove > 0){
            $html = strval($this->drawTemplate($type));
            return [
                'error' => '0' ,
                'message' => 'Movie deleted successfully!',
                'html' => $html
            ];
        }
        else
            return [
                'error' => '1' ,
                'message' => 'Movie doesn`t deleted!'
            ];
    }

    /**
     *@POST("/titles/media/dubbedVersions/saveChanges")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function dubbedVersionsSaveChanges()
    {
        if(!empty($this->request->Input('language')) && is_array($this->request->Input('language'))){
            foreach($this->request->Input('language') as $key => $val){
                if(is_array($val)){
                    foreach($val as $k => $v){
                        if(!array_key_exists($v, $this->getAllLocale())){
                            return [
                                'error' => '1' ,
                                'message' => 'Invalid locale'
                            ];
                        }
                        $mediaId = CHhelper::filterInputInt($k);
                        $mediaLocale = CHhelper::filterInput($v);

                        FilmsMedia::where('id', $k)->update([
                           'locale' => $v
                        ]);
                    }
                }
            }
        }
        return [
            'error' => '0' ,
            'message' => 'Dubbed Versions updatet successfully!'
        ];
    }

    private function tabExtras()
    {
        $film = $this->film;
    }

    public function extrasDestroy()
    {
        //
    }

    public function extrasImageUpload()
    {
        //
    }
	
    public function extrasImageDestroy()
    {
        //
    }

    /**
     *@POST("/titles/media/vimeo/saveChangesVimeo")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function saveChangesVimeo()
    {
        $trailerVimeo = CHhelper::filterInput($this->request->Input('trailerVimeo'));
        $movieVimeo = CHhelper::filterInput($this->request->Input('movieVimeo'));

        return Film::where('id', $this->filmId)->update([
            'trailerVimeo' =>  $trailerVimeo,
            'movieVimeo' =>  $movieVimeo
        ]);
    }

    /**
     *@POST("/titles/media/uploader/tabUploader")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function tabUploader()
	{
        if($this->request->ajax()){
            $type= $this->request->Input('type');
            return  $this->uploaderUpdateList($type);
        }else
	    	return $this->uploaderUpdateList('trailer', true);
	}
    private function uploaderUpdateList($mediaType = 'trailer', $isAjax = false)
    {
        $type = "mrss";
         if($isAjax){
            $filter = 'all';
        }else {
            $filter = $this->request->Input("filter").'-'.$this->request->Input("type");
            if($this->request->Input("type") == "bonus")
                $type="bonus";
        }

        return $this->getMedia('0bf4a7e03a9978d4ed2e5770adf23e33', $filter, $type, $mediaType);
    } 

     private function getMedia($hash, $filter='all', $type='mrss', $mediaType)
    {
		$film = $this->request->film;

        switch($filter)
        {
            case 'missing-trailer':
                    $res = $film->medias()
                        ->select('id', 'locale')
                        ->where('fk_films_media.type', 'trailer')
                        ->where('fk_films_media.source', '')
                        ->orWhere('fk_films_media.source', null)
                        ->where('fk_films_media.deleted', '0')
                        ->orderBy('fk_films_media.type', 'asc')
                        ->orderBy('fk_films_media.locale', 'asc')->get();
				break;
            case 'missing-movie':

                $res = $film->medias()
                    ->select('id', 'locale')
                    ->where('fk_films_media.type', 'movie')
                    ->where('fk_films_media.source', '')
                    ->orWhere('fk_films_media.source', null)
                    ->where('fk_films_media.deleted', '0')
                    ->orderBy('fk_films_media.type', 'asc')
                    ->orderBy('fk_films_media.locale', 'asc')->get();
                break;
            case 'missing-bonus':

                $res = $film->medias()
                    ->select('id', 'locale', 'track_index')
                    ->where('fk_films_media.type', 'bonus')
                    ->where('fk_films_media.source', '')
                    ->orWhere('fk_films_media.source', null)
                    ->where('fk_films_media.deleted', '0')
                    ->orderBy('fk_films_media.type', 'asc')
                    ->orderBy('fk_films_media.locale', 'asc')->get();
                break;
            case 'all-trailer':
            case 'all-movie':
            case 'all-bonus':
            case 'all':

                    $res = $film->medias()
                        ->select('id', 'locale', 'track_index')
                        ->where('fk_films_media.type', $mediaType)
                        ->where('fk_films_media.deleted', '0')
                        ->orderBy('fk_films_media.type', 'asc')
                        ->orderBy('fk_films_media.locale', 'asc')->get();
                break;
        }

        return $res;
    }
	
    /**
     *@POST("/titles/media/uploader/filterMediaUploaderUpdateList")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */	
	public function filterMediaUploaderUpdateList()
    {
        $film = $this->request->film;
        $allLocales = $this->getAllLocale();

        $media = [
            'uploader' => $this->tabUploader()
        ];

        return view('titles.titleMenegment.media.partials.uploader.partials.mediaUploader', compact('film', 'allLocales', 'media'));
    }

    /**
     *@POST("/titles/media/uploader/getAccountAmazonAccess")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function getAccountAmazonAccess()
    {
        return 1;
        $se = new amazonAssetsBuilder();
        return json_encode($se->getAmazonAssets());
    }


    /**
     *@POST("/titles/media/uploader/getUploaderHistory")
     * @Middleware("auth")
     * @Middleware("filmPermission")
     */
    public function getUploaderHistory()
    {
        $userInfo = Auth::user();

        $accountInfo = $userInfo->account;
        $accountId = $accountInfo->id;
		
        $bitjobs =  BitJobs::join('z_pass_through', 'z_bitjobs.pass_id', '=', 'z_pass_through.id')
							->join('cc_users', 'z_bitjobs.users_id', '=', 'cc_users.id')
							->select(['z_bitjobs.*', 'cc_users.person', 'z_pass_through.pass_through'])
							->where('z_bitjobs.films_id', $this->filmId)
							->where('z_bitjobs.accounts_id', $accountId)
							->get();

        $html =  view('titles.titleMenegment.media.partials.uploader.partials.uploaderHistory', compact('bitjobs'));
        $html = strval($html);

        return [
            'error' => '0',
            'message' => 'success',
            'html' => $html
        ];
    }
	
}

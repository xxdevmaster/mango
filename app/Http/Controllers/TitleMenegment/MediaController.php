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




class MediaController extends Controller
{

    public function mediaShow($id)
    {
        $current_menu = 'Media';
        $film = $this->getFilm($id);

        if(count($film) === 0)
            return view('errors.404', compact('current_menu'));
        $allLocales = $this->getAllLocale();
        $media = [
            'storage' => $this->tabStorage($id),
            'streaming' => $this->tabStreaming(),
            'movie' => $this->tabDubbedVersions($id, 'movie'),
            'trailer' => $this->tabDubbedVersions($id, 'trailer'),
            'extras' => $this->tabExtras($id),
            'uploadHistory' => $this->getUploaderHistory()
        ];
        return view('titles.titleMenegment.media.media', compact('current_menu','id', 'film', 'allLocales', 'media'));
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

    private function drawTemplate($filmId, $template)
    {
        $film = $this->getFilm($filmId);
        $allLocales = $this->getAllLocale();

        switch($template){
            case 'movie' :  $media = [
                                'movie' => $this->tabDubbedVersions($filmId, 'movie')
                            ];
                            $filmsMedia = $media['movie'];
                            return view('titles.titleMenegment.media.partials.dubbedVersions.partials.movieAndTrailer', compact('film', 'allLocales', 'filmsMedia'));break;
            case 'trailer' :  $media = [
                                'trailer' => $this->tabDubbedVersions($filmId, 'trailer')
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
     */
    public function getStreaming(Request $request)
    {
        if(!empty($request->input('cp')))
            $cp = '&provider_id='.CHhelper::filterInput($request->input('cp'));
    }

    private function tabStorage($id)
    {
        $userInfo = Auth::user();

        $accountInfo = $userInfo->account;
        $accountId = $accountInfo->id;

        $clientCdn = new Client();
        $urlCdn = $clientCdn->get('http://billing.cinehost.com/filmStorage/cdn?account_id='.$accountId.'&film_ids='.$id);
        $responseCdn = $urlCdn->send();
        $bodyCdn = $responseCdn->getBody(true);

        if(!$bodyCdn){
            return [
                'error' => '1',
                'message' => 'Curl Error'
            ];
        }
        $outCdn = json_decode($bodyCdn);

        $clientMez = new Client();
        $urlMez = $clientMez->get('http://billing.cinehost.com/filmStorage/mezzanine?account_id='.$accountId.'&film_ids='.$id);
        $responseMez = $urlMez->send();
        $bodyMez = $responseMez->getBody(true);

        if(!$bodyMez){
            return [
                'error' => '1',
                'message' => 'Curl Error'
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

    private function tabDubbedVersions($id, $type)
    {
        return FilmsMedia::where('films_id', $id)->where('type', $type)->where('deleted', '0')->get();
    }

    /**
     *@POST("/titles/media/dubbedVersions/dubbedVersionsCreate")
     * @Middleware("auth")
     */
    public function dubbedVersionsCreate(Request $request)
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

        if(empty($request->Input('type')) || $request->Input('type') != 'movie' && $request->Input('type') != 'trailer'){
            return [
                'error' => '1' ,
                'message' => 'Dubbed version type doesnt exixst'
            ];
        }

        $filmId = CHhelper::filterInputInt($request->Input('filmId'));
        $locale = CHhelper::filterInput($request->Input('locale'));
        $type = CHhelper::filterInput($request->Input('type'));

        $createmovie = FilmsMedia::create([
            'films_id' => $filmId,
            'locale' => $locale,
            'type' => $type
        ])->id;

        if($createmovie > 0){
            $html = strval($this->drawTemplate($filmId, $type));
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
     */
    public function dubbedVersionsRemove(Request $request)
    {
        if(empty($request->Input('filmId')) && empty($request->Input('movieId'))){
            return [
                'error' => '1' ,
                'message' => 'Film or movie identifier doesnt exixst'
            ];
        }
        if(!is_numeric($request->Input('filmId')) && !is_numeric($request->Input('movieId'))){
            return [
                'error' => '1' ,
                'message' => 'Identifier film or movie not valid format'
            ];
        }

        if(count($this->getFilm($request->Input('filmId'))) === 0){
            return [
                'error' => '1' ,
                'message' => 'You dont have perrmisions'
            ];
        }

        $filmId = CHhelper::filterInputInt($request->Input('filmId'));
        $movieId = CHhelper::filterInputInt($request->Input('movieId'));
        $type = CHhelper::filterInput($request->Input('type'));

        $movieRemove =  FilmsMedia::where('id', $movieId)->where('films_id', $filmId)->update([
            'deleted' => '1'
        ]);

        if($movieRemove > 0){
            $html = strval($this->drawTemplate($filmId, $type));
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
     */
    public function dubbedVersionsSaveChanges(Request $request)
    {
        if(empty($request->Input('filmId'))){
            return [
                'error' => '1' ,
                'message' => 'Film identifier doesnt exixst'
            ];
        }
        if(!is_numeric($request->Input('filmId'))){
            return [
                'error' => '1' ,
                'message' => 'Identifier film not valid format'
            ];
        }

        if(count($this->getFilm($request->Input('filmId'))) === 0){
            return [
                'error' => '1' ,
                'message' => 'You dont have perrmisions'
            ];
        }

        $filmId = CHhelper::filterInputInt($request->Input('filmId'));

        if(!empty($request->Input('language')) && is_array($request->Input('language'))){
            foreach($request->Input('language') as $key => $val){
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

    private function tabExtras($id)
    {
        $film = $this->getFilm($id);

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
     */
    public function saveChangesVimeo(Request $request)
    {
        if(empty($request->Input('filmId'))){
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
        if(count($this->getFilm($request->Input('filmId'))) === 0){
            return [
                'error' => '1' ,
                'message' => 'You dont have perrmisions'
            ];
        }

        $filmId = CHhelper::filterInputInt($request->Input('filmId'));
        $trailerVimeo = CHhelper::filterInput($request->Input('trailerVimeo'));
        $movieVimeo = CHhelper::filterInput($request->Input('movieVimeo'));

        return Film::where('id', $filmId)->update([
            'trailerVimeo' =>  $trailerVimeo,
            'movieVimeo' =>  $movieVimeo
        ]);
    }

    public function uploaderFileUpload()
    {
        //
    }

    /**
     *@POST("/titles/media/uploader/getUploaderHistory")
     * @Middleware("auth")
     */
    public function getUploaderHistory()
    {
        $bitjobs = array();
        $html =  view('titles.titleMenegment.media.partials.uploader.history.uploaderHistory', compact('bitjobs'));
        $html = strval($html);
        return [
            'error' => '0',
            'message' => 'success',
            'html' => $html
        ];
    }
	
}

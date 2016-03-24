<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\Libraries\CHhelper\CHhelper;
use App\Models\Silders;
use App\Models\FilmSlidersImages;
use App\Store;
use Aws\Common\Aws;
class SliderController extends Controller
{
    private $request;

    private $authUser;

    private $accountID;

    private $storeID;

    private $companyID;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->authUser = Auth::user();
        $this->accountID = $this->authUser->account->id;
        $this->storeID = $this->authUser->account->platforms_id;
        $this->companyID = $this->authUser->account->companies_id;
    }

    public function sliderShow()
    {
        $films = $this->getStoreFilms();
        return view('store.slider.slider', compact('films'), ['slider' => $this->getMainSliderID(), 'storeID' => $this->storeID]);
    }

    public function getMainSliderID()
    {
        if($this->storeID != 0 || $this->companyID == 1) {
            $this->slider = Silders::where('channel_id', $this->storeID)->get()->keyBy('id');

            if($this->slider->isEmpty())
                Silders::create([
                    'channel_id' => $this->storeID,
                    'title' => 'Main Slider'
                ])->id;

            return $this->slider;
        }
    }

    public function getImageitems($sliderID)
    {
        return FilmSlidersImages::where('sliders_id', $sliderID)->orderBy('position', 'asc')->get()->keyBy('id');
    }

    private function getStoreFilms()
    {
        return Store::find($this->storeID)->storesFilms(0)->get()->keyBy('id');
    }

    /**
     *@POST("/store/slider/uploadImage")
     * @Middleware("auth")
     */
    public function uploadImage()
    {
        $s3AccessKey = 'AKIAJPIY5AB3KDVIDPOQ';
        $s3SecretKey = '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';
        $region    	 = 'us-east-1';
        $bucket		 = 'cinecliq.assets';

        $fileTypes = array('jpg','jpeg','png','PNG','JPG','JPEG','svg','SVG'); // File extensions

        $s3path = $this->request->file('Filedata');
        $s3name = $s3path->getClientOriginalName();
        $s3mimeType = $s3path->getClientOriginalExtension();

        if(in_array($s3mimeType, $fileTypes)){
            $s3 = AWS::factory([
                'key'    => $s3AccessKey,
                'secret' => $s3SecretKey,
                'region' => $region,
            ])->get('s3');

            $s3->putObject([
                'Bucket' => $bucket,
                'Key'    => 'wls/'.$this->storeID.'/'.$s3name,
                'Body'   => fopen($s3path, 'r'),
                'SourceFile' => $s3path,
                'ACL'    => 'public-read',
            ]);

            FilmSlidersImages::create([
                'filename' => $s3name ,
                'sliders_id' => $this->request->Input('sliderID')
            ]);

            $films = $this->getStoreFilms();

            return [
                'error' => 0,
                'message' => $s3name ,
                'html' => view('store.slider.list', compact('films'), ['slider' => $this->getMainSliderID(), 'storeID' => $this->storeID])->render()
            ];
        }else
            $response = [
                'error' => 1,
                'message' => $s3mimeType.' is invalid file type'
            ];

        return $response;
    }

    /**
     *@POST("/store/slider/save")
     * @Middleware("auth")
     */
    public function save()
    {
        foreach($this->request->Input() as $key => $val) {
            foreach($val as $k => $v) {
                $slideID = CHhelper::filterInputInt($k);
                FilmSlidersImages::where('id', $slideID)->update([
                    'title' => CHhelper::filterInput($v['title']) ,
                    'brief' => CHhelper::filterInput($v['brief']) ,
                    'url' => CHhelper::filterInput($v['url']) ,
                    'films_id' => CHhelper::filterInputInt($v['filmsID']) ,
                    'position' => CHhelper::filterInputInt($v['position']) ,
                ]);
            }
        }
    }

    /**
     *@POST("/store/slider/removeSlide")
     * @Middleware("auth")
     */
    public function removeSlide()
    {
        $slideID = (!empty($this->request->Input('slideID')) && is_numeric($this->request->Input('slideID'))) ? CHhelper::filterInputInt($this->request->Input('slideID')) : false;
        if($slideID)
            FilmSlidersImages::destroy($slideID);

        $films = $this->getStoreFilms();
        return view('store.slider.list', compact('films'), ['slider' => $this->getMainSliderID(), 'storeID' => $this->storeID])->render();
    }
}

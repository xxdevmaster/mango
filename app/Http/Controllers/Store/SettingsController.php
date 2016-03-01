<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Guzzle\Http\Client;
use App\Store;
use Auth;
use App\Libraries\CHhelper\CHhelper;
use Aws\Common\Aws;

class SettingsController extends Controller
{
    private $request;

    private $authUser;

    private $storeID;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->authUser = Auth::user();
        $this->storeID = $this->authUser->account->platforms_id;
    }

    public function settingsShow()
    {
        //$template = $this->getAllTemplates();
        $store = $this->getStore();
        return view('store.settings.settings', compact('store', 'template'));
    }

    private function getStore()
    {
        return Store::where('cc_channels.id', $this->storeID)
                    ->join('cc_accounts', 'cc_accounts.platforms_id', '=', 'cc_channels.id')
                    ->select('cc_channels.id', 'favicon', 'seo_image', 'seo_title', 'seo_keys', 'seo_description', 'fbpage', 'twpage', 'ga_code', 'terms', 'cc_accounts.source')
                    ->get()->first();
    }

    /**
     *@POST("/store/settings/drawEditSettings")
     * @Middleware("auth")
     */
    public function drawEditSettings()
    {
        return view('store.settings.settingsEdit_form', ["store" => $this->getStore()])->render();
    }

    /**
     *@POST("/store/settings/drawStore")
     * @Middleware("auth")
     */
    public function drawStore()
    {
        return view('store.settings.settings_tab', ["store" => $this->getStore()])->render();
    }

    /**
     *@POST("/store/settings/saveStore")
     * @Middleware("auth")
     */
    public function saveStore()
    {
        Store::where('id', $this->storeID)->update([
            'seo_title' => CHhelper::filterInput($this->request->Input('seo_title')) ,
            'seo_keys' => CHhelper::filterInput($this->request->Input('seo_keys')) ,
            'seo_description' => CHhelper::filterInput($this->request->Input('seo_description')) ,
            'fbpage' => CHhelper::filterInput($this->request->Input('fbpage')) ,
            'twpage' => CHhelper::filterInput($this->request->Input('twpage')) ,
            'ga_code' => CHhelper::filterInput($this->request->Input('ga_code')) ,
            'terms' => CHhelper::filterInput($this->request->Input('terms')) ,
            'favicon' => CHhelper::filterInput($this->request->Input('favicon')) ,
            'seo_image' => CHhelper::filterInput($this->request->Input('seo_image')) ,
        ]);
        return $this->drawStore();
    }

    /**
     *@POST("/store/settings/removeFavicon")
     * @Middleware("auth")
     */
    public function removeFavicon()
    {
        return Store::where('id', $this->storeID)->update([
            'favicon' => 'def_favicon.png' ,
        ]);
    }

    /**
     *@POST("/store/settings/removeSeoImage")
     * @Middleware("auth")
     */
    public function removeSeoImage()
    {
        return Store::where('id', $this->storeID)->update([
            'seo_image' => 'def_seo_image.jpg' ,
        ]);
    }

    /**
     *@POST("/store/settings/uploadFavicon")
     * @Middleware("auth")
     */
    public function uploadFavicon()
    {
        $s3AccessKey = 'AKIAJPIY5AB3KDVIDPOQ';
        $s3SecretKey = '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';
        $region    	 = 'us-east-1';
        $bucket		 = 'cinecliq.assets';
        $maxSize = 100*1024;

        $fileTypes = array('jpg','jpeg','png','PNG','JPG','JPEG','svg','SVG'); // File extensions

        $s3path = $this->request->file('Filedata');
        $s3name = $s3path->getClientOriginalName();
        $s3mimeType = $s3path->getClientOriginalExtension();
        $s3fileSize = $s3path->getClientSize();
        list($_width, $_height) = @getimagesize($s3path);

        if(in_array($s3mimeType, $fileTypes)){
            if ($_width > 32 || $_height > 32){
                return [
                    'error' => '1',
                    'message' => 'Unable to upload. Please make sure your image is 32x32px.'
                ];
            }

            if ($s3fileSize > $maxSize){
                return [
                    'error' => '1',
                    'message' => 'Max File size 100kb.'
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
     *@POST("/store/settings/uploadSeoImage")
     * @Middleware("auth")
     */
    public function uploadSeoImage()
    {
        $s3AccessKey = 'AKIAJPIY5AB3KDVIDPOQ';
        $s3SecretKey = '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';
        $region    	 = 'us-east-1';
        $bucket		 = 'cinecliq.assets';

        $fileTypes = array('jpg','jpeg','png','PNG','JPG','JPEG','svg','SVG'); // File extensions

        $s3path = $this->request->file('Filedata');
        $s3name = $s3path->getClientOriginalName();
        $s3mimeType = $s3path->getClientOriginalExtension();
        list($_width, $_height) = @getimagesize($s3path);

        if(in_array($s3mimeType, $fileTypes)){
            if ($_width > 1200 || $_height > 1200){
                return [
                    'error' => '1',
                    'message' => 'Unable to upload. Please make sure your image is 1200x1200px.'
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

    public function getAllTemplates()
    {
        $clientCdn = new Client();
        $urlCdn = $clientCdn->get('http://cactus.cinehost.tv/backstage/templates');
        $responseCdn = $urlCdn->send();
        $bodyCdn = $responseCdn->getBody(true);

        if(!$bodyCdn){
            return [
                'error' => '1',
                'message' => 'Curl Error'
            ];
        }
        return json_decode($bodyCdn);


        /*$url = 'http://cactus.cinehost.tv/backstage/templates';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        $response = curl_exec($ch);
        if($response === false)
        {
            echo ' curl: ' . curl_error($ch);
        }
        $choutput = json_decode($response);

        usort($choutput, function($a, $b)
        {
            return strcmp($a->position, $b->position);
        });

        curl_close($ch);
        return $choutput;*/
    }

    public function activateTemplate(){
        $params = array(
            "wlid" =>$_SESSION['WL_ID'],
            "template_id" =>$data['template_id']
        );

        BaseElements::httpPost("http://cactus.cinehost.tv/backstage/platform/update",$params);

        Store::where('id', $this->storeID)->update([
            'template_id' => CHhelper::filterInput($this->request->Input('template_id')) ,
        ]);

        //return $this->drawTemplates();
    }

    /**
     *@POST("/store/settings/saveStoreTemplates")
     * @Middleware("auth")
     */
    public function saveStoreTemplates(){

        return Store::where('id', $this->storeID)->update([
            'slider_width' => CHhelper::filterInput($this->request->Input('slider_width')) ,
            'slider_height' => CHhelper::filterInput($this->request->Input('slider_height')) ,
            'seo_title' => CHhelper::filterInput($this->request->Input('seo_title')) ,
            'seo_keys' => CHhelper::filterInput($this->request->Input('seo_keys')) ,
            'seo_description' => CHhelper::filterInput($this->request->Input('seo_description')) ,
            'fbpage' => CHhelper::filterInput($this->request->Input('fbpage')) ,
            'twpage' => CHhelper::filterInput($this->request->Input('twpage')) ,
            'ga_code' => CHhelper::filterInput($this->request->Input('ga_code')) ,
            'terms' => CHhelper::filterInput($this->request->Input('terms')) ,
        ]);
    }
}

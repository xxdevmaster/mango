<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\Store;
use App\Libraries\CHhelper\CHhelper;
use Aws\Common\Aws;

class ProfileController extends Controller
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
	
	public function profileShow()
	{
		$store = $this->getStore();
		return view('store.profile.profile', compact('store'));
	}

	private function getStore()
	{
		return Store::where('id', $this->storeID)->select('id', 'title', 'person', 'address', 'phone', 'email', 'website', 'brief', 'logo')->get()->first();
	}

	/**
	 *@POST("/store/profile/drawEditstore")
	 * @Middleware("auth")
	 */
	public function drawEditstore()
	{
		return view('store.profile.profileEdit_partial', ["store" => $this->getStore()])->render();
	}

	/**
	 *@POST("/store/profile/drawStore")
	 * @Middleware("auth")
	 */
	public function drawStore()
	{
		return view('store.profile.profileInfo_partial', ["store" => $this->getStore()])->render();
	}

	/**
	 *@POST("/store/profile/editStore")
	 * @Middleware("auth")
	 */
	public function editStore()
	{
		Store::where('id', $this->storeID)->update([
			'title' => CHhelper::filterInput($this->request->Input('title')) ,
			'person' => CHhelper::filterInput($this->request->Input('person')) ,
			'address' => CHhelper::filterInput($this->request->Input('address')) ,
			'phone' => CHhelper::filterInput($this->request->Input('phone')) ,
			'email' => CHhelper::filterInput($this->request->Input('email')) ,
			'website' => CHhelper::filterInput($this->request->Input('website')) ,
			'brief' => CHhelper::filterInput($this->request->Input('brief')) ,
		]);
		return $this->drawStore();
	}

	/**
	 *@POST("/store/profile/removeLogoCP")
	 * @Middleware("auth")
	 */
	public function removeLogoCP()
	{
		Store::where('id', $this->storeID)->update([
			'logo' => 'nologo.png',
		]);
	}
	/**
	 *@POST("/store/profile/uploadLogo")
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

			Store::where('id', $this->storeID)->update([
				'logo' => $s3name
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
}

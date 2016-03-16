<?php

namespace App\Http\Controllers\Xchange;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\Company;
use App\Libraries\CHhelper\CHhelper;
use Aws\Common\Aws;


class ProfileController extends Controller
{
	private $request;

	private $authUser;

	private $companyID;
	
    public function __construct(Request $request)
	{
		$this->request = $request;
		$this->authUser = Auth::user();
		$this->companyID = $this->authUser->account->company->id;
	}
	
    public function profileShow()
    {
        $CP = $this->getCp();
        return view('Xchange.profile.cPProfile', compact('CP'));
    }

    private function getCp()
    {
        return Company::where('id', $this->companyID)->get()->first();
    }

    /**
     *@POST("/xchange/profile/drawCP")
     * @Middleware("auth")
     */
    public function drawCP()
    {
        return view('xchange.profile.cPInfo_partial', ["CP" => $this->getCp()])->render();
    }

    /**
     *@POST("/xchange/profile/drawEditCP")
     * @Middleware("auth")
     */
    public function drawEditCP()
    {
        $CP = $this->getCp();
        return view('xchange.profile.editCPInfo_partial', compact('CP'))->render();
    }

    /**
     *@POST("/xchange/profile/editCP")
     * @Middleware("auth")
     */
    public function editCP()
    {
        Company::where('id', $this->companyID)->update([
            'title' => CHhelper::filterInput($this->request->Input('title')) ,
            'person' => CHhelper::filterInput($this->request->Input('person')) ,
            'address' => CHhelper::filterInput($this->request->Input('address')) ,
            'phone' => CHhelper::filterInput($this->request->Input('phone')) ,
            'email' => CHhelper::filterInput($this->request->Input('email')) ,
            'website' => CHhelper::filterInput($this->request->Input('website')) ,
            'brief' => CHhelper::filterInput($this->request->Input('brief')) ,
        ]);
        return $this->drawCP();
    }

    /**
     *@POST("/xchange/profile/removeLogoCP")
     * @Middleware("auth")
     */	
	public function removeLogoCP()
	{
		Company::where('id', $this->companyID)->update([
			'logo' => 'nologo.png',
		]);		
	}

	/**
     *@POST("/xchange/profile/uploadLogo")
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

			Company::where('id', $this->companyID)->update([
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

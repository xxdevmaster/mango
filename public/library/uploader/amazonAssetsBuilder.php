<?php
require dirname(__FILE__).'/../../vendor/autoload.php';
use Aws\Common\Aws;
use Aws\Common\Enum\Region;
use Aws\S3\Enum\CannedAcl;
use Aws\S3\Exception\S3Exception;
use Aws\Iam\Exception;
use Guzzle\Http\EntityBody;

class amazonAssetsBuilder {
    public $accountID;
    public $o;
   
    public function __construct($accountID){
        $this->accountID = $accountID;
    }

    public function getAmazonAssets(){
        $res= G('DB')->query($q="SELECT cc_amazone_assets.* FROM cc_amazone_assets WHERE cc_amazone_assets.accounts_id='".$this->accountID."' ")->fetch(PDO::FETCH_OBJ);
        if($res)
        {
            $obj = array('bucket'=>$res->bucket,'region'=>$res->region,'secret_key'=>$res->secret_key,'access_key'=>$res->access_key);
        }
        else{
        //    accountInfo
            $obj = $this->createAmazonAsset();
        }
        return $obj;
    }
    public function createAmazonAsset(){
        $access_key = 'AKIAJPIY5AB3KDVIDPOQ';
        $secret_key = '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';
        $region     = Region::US_EAST_1;
        $accountInfo = BaseElements::getAccountInfo( $this->accountID );
        $username = preg_replace( "/[^a-z]/i", "", $accountInfo->title ).'_ACID_.'.$this->accountID;
        
        try {
            $backet_name = "zero.".$username.".assets";
            $Iam = Aws::factory(array(
                            'key'    => $access_key,
                            'secret' => $secret_key,
                            'region' => $region,
            ))->get('Iam');

            $resultUser = $Iam->createUser(array(
                            'Path' => '/zero/',
                            // UserName is required
                            'UserName' => $username,
            ));

            $resultAccess = $Iam->createAccessKey(array(
                            'UserName' => $username,
            ));

            $resaccess = $resultAccess->toArray();

            $data_to_save["secret_key"] = $resaccess["AccessKey"]["SecretAccessKey"];
            $data_to_save["access_key"] = $resaccess["AccessKey"]["AccessKeyId"];
            $S3 = Aws::factory(array(
                            'key'    => $access_key,
                            'secret' => $secret_key,
                            'region' => $region,
            ))->get('S3');
            
            
            $this->attachUserPolicy($Iam,$username);	
            $this->attachBucket($S3,$backet_name);	

            $resultn = $Iam->listUserPolicies(array(
                            // UserName is required
                            'UserName' => $username,
            ));

            $q = "INSERT INTO  cc_amazone_assets (accounts_id,bucket,region,secret_key,access_key) VALUES ('".$this->accountID."','".$backet_name."','".$region."','".$data_to_save['secret_key']."','".$data_to_save['access_key']."')";
            error_log($q);
            G('DB')->query($q);
		
        } catch (Exception $e) {echo 'Invalid file.';}
         
        return $this->getAmazonAssets();
    }
    public function  attachUserPolicy($Iam,$username){
	$policy  = array("Version" => "2012-10-17", "Statement"=>array(
		array(
			"Effect" => "Allow",
			"Action" => "s3:*",
			"Resource" => "*"
		)
	));
	
	$Iam->putUserPolicy(array(
			// UserName is required
			'UserName' => $username,
			// PolicyName is required
			'PolicyName' => $username.'policy',
			// PolicyDocument is required
			'PolicyDocument' => json_encode($policy),
	));
    }
    public function  attachBucket($S3,$backet_name){
	$S3->createBucket(array('Bucket' => $backet_name));
            $S3->waitUntil('BucketExists', array('Bucket' => $backet_name));
            $S3->putBucketCors(array(
            'Bucket' => $backet_name,
            'CORSRules' => array(
                    array(
                        'AllowedHeaders' => array('*'),
                        'AllowedOrigins' => array('*'),
                        'AllowedMethods' => array('HEAD','PUT','GET','POST','DELETE'),
                        'ExposeHeaders' => array('ETag','x-amz-meta-custom-header')                        
                    ) 
                )
            ));
            $S3->putBucketPolicy(array(
                'Bucket' => $backet_name,
                'Policy' => json_encode(array(
                    "Version"=> "2008-10-17",
                     "Id"=>"ZencoderBucketPolicy_".$backet_name,
                    'Statement' => array(
                        array(
                            "Sid"=> "Stmt1295042087538",
                            "Effect"=>"Allow",
                            "Principal"=>array("AWS"=>"arn:aws:iam::395540211253:root"),
                            "Action"=> "s3:GetObject",
                            "Resource"=>"arn:aws:s3:::".$backet_name."/*"
                                                       
                        ),
                        array(
                            "Sid"=> "Stmt1295042087538",
                            "Effect"=>"Allow",
                            "Principal"=>array("AWS"=>"arn:aws:iam::395540211253:root"),
                            "Action"=>array("s3:ListBucketMultipartUploads","s3:GetBucketLocation"),
                            "Resource"=>"arn:aws:s3:::".$backet_name
                                                       
                        ),
                    )
                ))
            ));
    }


} 
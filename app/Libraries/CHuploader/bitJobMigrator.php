<?php
namespace App\Libraries\CHuploader;

/**
 * Created by PhpStorm.
 * User: edgar
 * Date: 3/10/15
 * Time: 3:21 PM
 */
//require dirname(__FILE__).'/../../vendor/autoload.php';
use bitcodin\Bitcodin;
use bitcodin\VideoStreamConfig;
use bitcodin\S3OutputConfig;
use bitcodin\AudioStreamConfig;
use bitcodin\Job;
use bitcodin\JobConfig;
use bitcodin\Input;
use bitcodin\HttpInputConfig;
use bitcodin\EncodingProfile;
use bitcodin\EncodingProfileConfig;
use bitcodin\ManifestTypes;
use bitcodin\Output;
use bitcodin\AwsRegion;
use bitcodin\FtpOutputConfig;

class bitJobMigrator {
    private $urlprefix = 'sftp://demandcliq.upload.akamai.com';
    private $media;
    private $dt;
    private $dir;
    //private $file;
    private $output_bucket;
    private $zencoder;
    public $accountID;
    private $quality;

    public function __construct($accountID){
        $this->accountID = $accountID;
        Bitcodin::setApiToken('0c96481e564313c6519102a323e9aacfe33cd84e93b9a8d93bb680bace598475');
    }

    public function getJobs(){
        return array();//$this->zencoder->jobs->index();
    }


    public function createJob($params)
    {
        $this->createDashMigrateJob($params);
        //$this->createTestJob($params);
    }

    private function levels($quality)
    {
        $levels = array(
            '400'=>array('width'=>480),
            '800'=>array('width'=>640),
            '1200'=>array('width'=>960),
            '2400'=>array('width'=>1280),
            '4600'=>array('width'=>1920)
        );
        return $levels;
    }


    public function listAllJobs()
    {
        $jobs = Job::getListAll();
        return $jobs;
    }

    public function createTestJob($params)
    {
    	$this->quality = $params["quality"]?$params["quality"]:"hd";
    
	    $inputConfig = new HttpInputConfig();
	    $inputConfig->url = 'http://cinehost.progressive.edgesuite.net/trailers/2015-09/02627/0/en/1920.02627.0.en.mp4';
	    $input = Input::create($inputConfig);
	    
	    /* CREATE AUDIO STREAM CONFIGS */
		$audioStreamConfig = new AudioStreamConfig();
		$audioStreamConfig->bitrate = 128000;
		
		$encodingProfileConfig = new EncodingProfileConfig();
		$encodingProfileConfig->name = 'CinehostFullHD';
		$encodingProfileConfig->audioStreamConfigs[] = $audioStreamConfig;
		
	    /* CREATE VIDEO STREAM CONFIG */
	    $levels = $this->levels($this->quality);
	    foreach($levels as $br=>$dim)
	    {
		    $VSC = new VideoStreamConfig();
			$VSC->bitrate = $br*1024;
			$VSC->width = $dim['width'];
			$VSC->height = $dim['width']*9/16;
			
			$encodingProfileConfig->videoStreamConfigs[] = $VSC;
	    }		
		
		/* CREATE ENCODING PROFILE */
		$encodingProfile = EncodingProfile::create($encodingProfileConfig);
		
		$jobConfig = new JobConfig();
		$jobConfig->encodingProfile = $encodingProfile;
		$jobConfig->input = $input;
		$jobConfig->manifestTypes[] = ManifestTypes::M3U8;
		$jobConfig->manifestTypes[] = ManifestTypes::MPD;
		
		/* CREATE JOB */
		$job = Job::create($jobConfig);
		
		/* WAIT TIL JOB IS FINISHED */
		do{
		    $job->update();
		    sleep(1);
		} while($job->status != Job::STATUS_FINISHED);
		

		$outputConfig = new S3OutputConfig();
		$outputConfig->name         = "CinehostS3Output";
		$outputConfig->accessKey    = "AKIAJPIY5AB3KDVIDPOQ";
		$outputConfig->secretKey    = "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";
		$outputConfig->bucket       = "uone";
		$outputConfig->region       = AwsRegion::US_EAST_1;
		$outputConfig->prefix       = "xetta";
		$outputConfig->makePublic   = true;                            // This flag determines whether the files put on S3 will be publicly accessible via HTTP Url or not
		$outputConfig->host         = "s3-us-east-1.amazonaws.com";     // OPTIONAL

		
		$output = Output::create($outputConfig);
		var_dump($output);
		
		sleep(15);
		/* TRANSFER JOB OUTPUT */
		$jobTransfer = $job->transfer($output);

		var_dump($jobTransfer);
    }


    public function createDashMigrateJob($params)
    {
        $this->quality = $params["quality"]?$params["quality"]:"hd";
        $this->media = $params["media"];
        $id = ltrim($params["id"],'0');
        $this->dt = $params["dt"];
        $this->track = $params["track"];
        $this->locale = $params["locale"];
        $this->path = $params["path"];
        $this->output_bucket = "cinehost.streamer";
        $levels = $this->levels($this->quality);
        $this->outdir = "{$this->media}/".(implode('/',str_split((str_pad($id,5,'0',STR_PAD_LEFT)))))."/{$this->locale}/{$this->track}";

        try
        {
            $inputConfig = new HttpInputConfig();
            $inputConfig->url = 'http://cinehost.progressive.edgesuite.net/'.$this->path;
            $input = Input::create($inputConfig);

            /* CREATE AUDIO STREAM CONFIGS */
            $audioStreamConfig = new AudioStreamConfig();
            $audioStreamConfig->bitrate = 128000;

            $encodingProfileConfig = new EncodingProfileConfig();
            $encodingProfileConfig->name = 'CinehostFullHD';
            $encodingProfileConfig->audioStreamConfigs[] = $audioStreamConfig;

            /* CREATE VIDEO STREAM CONFIG */
            foreach($levels as $br=>$dim)
            {
                $VSC = new VideoStreamConfig();
                $VSC->bitrate = $br*1024;
                $VSC->width = $dim['width'];
                $VSC->height = $dim['width']*9/16;

                $encodingProfileConfig->videoStreamConfigs[] = $VSC;
            }

            /* CREATE ENCODING PROFILE */
            $encodingProfile = EncodingProfile::create($encodingProfileConfig);

            $jobConfig = new JobConfig();
            $jobConfig->encodingProfile = $encodingProfile;
            $jobConfig->input = $input;
            $jobConfig->manifestTypes[] = ManifestTypes::M3U8;
            $jobConfig->manifestTypes[] = ManifestTypes::MPD;

            /* CREATE JOB */
            $job = Job::create($jobConfig);



            /* WAIT TIL JOB IS FINISHED */

//            do{
//                $job->update();
//                sleep(1);
//            } while($job->status != Job::STATUS_FINISHED);


//            $outputConfig = new S3OutputConfig();
//            $outputConfig->name         = "CinehostS3Output";
//            $outputConfig->accessKey    = "AKIAJPIY5AB3KDVIDPOQ";
//            $outputConfig->secretKey    = "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";
//            $outputConfig->bucket       = "uone";
//            $outputConfig->region       = AwsRegion::US_EAST_1;
//            $outputConfig->prefix       = "xetta";
//            $outputConfig->makePublic   = true;                            // This flag determines whether the files put on S3 will be publicly accessible via HTTP Url or not
//            $outputConfig->host         = "s3-us-east-1.amazonaws.com";     // OPTIONAL
//
//
//            $output = Output::create($outputConfig);

            /* TRANSFER JOB OUTPUT */
//            $jobTransfer = $job->transfer($output);

//            $outurls = array();
//
//            foreach($levels as $br=>$v)
//            {
//                $outurls['hls']['files'][]="s3://{$this->output_bucket}.s3.amazonaws.com/{$this->outdir}/hls/{$br}.m3u8";
//                $outurls['dash']['files'][]="s3://{$this->output_bucket}.s3.amazonaws.com/{$this->outdir}/dash/{$br}k/rendition.mpd";
//            }
//
//            $outurls['hls']['playlist']="s3://{$this->output_bucket}.s3.amazonaws.com/{$this->outdir}/hls/playlist.m3u8";
//            $outurls['dash']['playlist']="s3://{$this->output_bucket}.s3.amazonaws.com/{$this->outdir}/dash/playlist.mpd";



            $passThrough = str_replace("'",'"',json_encode(array('accountID'=>$this->accountID,'filmID'=>$id,'date'=>$this->dt,'locale'=>$params["locale"],'track'=>$params["track"],'media'=>$this->media,'quality'=>$this->quality,'BASEPATH'=>$this->outdir."/","JOB"=>$job->jobId)));
            G('DB')->query("INSERT INTO z_pass_through (pass_through) VALUES ('$passThrough')");
            $passid = G('DB')->lastInsertId();

            G('DB')->query("INSERT INTO z_bitjobs (accounts_id,films_id,job_id,job_status,pass_id) VALUES ('$this->accountID','$id','$job->jobId','$job->status','$passid')");

        } catch (Exception $e) { }
    }

} 
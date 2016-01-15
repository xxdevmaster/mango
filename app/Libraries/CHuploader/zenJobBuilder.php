<?php
namespace App\Libraries\CHuploader;

/**
 * Created by PhpStorm.
 * User: gaz
 * Date: 1/10/15
 * Time: 3:21 PM
 */
//require dirname(__FILE__).'/../../vendor/autoload.php';
use bitcodin\AudioStreamConfig;
use bitcodin\Bitcodin;
use bitcodin\CombinedWidevinePlayreadyDRMConfig;
use bitcodin\DRMEncryptionMethods;
use bitcodin\EncodingProfile;
use bitcodin\EncodingProfileConfig;
use bitcodin\Input;
use bitcodin\Job;
use bitcodin\JobConfig;
use bitcodin\JobSpeedTypes;
use bitcodin\ManifestTypes;
use bitcodin\S3InputConfig;
use bitcodin\VideoStreamConfig;


class bitJobBuilder {
    private $urlprefix = 'sftp://demandcliq.upload.akamai.com';
    private $media;
    private $dt;
    private $dir;
    private $file;
    private $input_bucket;
    private $output_bucket;
    private $zencoder;
    public $accountID;
    private $quality;

    public function __construct($accountID,$userID){
        $this->accountID = $accountID;
        $this->userID = $userID;
        Bitcodin::setApiToken('0c96481e564313c6519102a323e9aacfe33cd84e93b9a8d93bb680bace598475');
    }

    public function listAllJobs()
    {
        $jobs = Job::getListAll();
        return $jobs;
    }

    public function createJob($params)
    {
        if($params["quality"] == 'offline')
            return $this->createDashDVDJob($params);
        else {
            return $this->createDashUploadJob($params);
        }
    }

    private function levels($quality)
    {
        $levels = array(
            '400'=>array('width'=>480),
            '800'=>array('width'=>640),
            '1200'=>array('width'=>960),
            '2400'=>array('width'=>1280)
        );
        if($quality == 'hd')
            $levels['4600'] = array('width'=>1920);

        if($quality == 'offline')
            $levels = array('4600' => array('width'=>1920));

        return $levels;
    }

    public function getS3Credentials()
    {
        $q = "SELECT * FROM cc_amazone_assets WHERE accounts_id='{$this->accountID}'";
        $this->awsinfo = G('DB')->query($q)->fetch(PDO::FETCH_ASSOC);
    }

    public function createDashUploadJob($params)
    {
        $this->getS3Credentials(); // fetch credentials for input
        $this->quality = $params["quality"]?$params["quality"]:"hd";
        $this->media = $params["media"];
        $id = ltrim($params["id"],'0');
        $this->dt = $params["dt"];
        $this->drm = $params["drm"];
        $this->track = $params["track"];
        $this->locale = $params["locale"];
        //$this->path = $params["path"];
        $this->output_bucket = "cinehost.streamer";

        if($params["batch"]) { // drive based batch job
            $this->file = $params["file"];
            $this->input_path = substr($params['full_path'],1);
            $this->input_file_path = substr($params['full_path'],1);
        }
        else
        {
            $this->file = "MASTER." . $this->media . "." . $id . ".mp4";
            $this->input_path = "{$this->media}/{$this->dt}/" . (str_pad($id, 5, '0', STR_PAD_LEFT)) . "/{$this->track}/{$this->locale}";
            $this->input_file_path = "{$this->input_path}/{$this->file}";
        }

        $this->input_bucket = $this->awsinfo['bucket'];
        $levels = $this->levels($this->quality);
        $this->outdir = "{$this->media}/".(implode('/',str_split((str_pad($id,5,'0',STR_PAD_LEFT)))))."/{$this->locale}/{$this->track}";

        try
        {
            $inputConfig = new S3InputConfig();
            $inputConfig->accessKey = $this->awsinfo['access_key'];
            $inputConfig->secretKey = $this->awsinfo['secret_key'];
            $inputConfig->bucket    = $this->awsinfo['bucket'];
            $inputConfig->region    = $this->awsinfo['region'];
            $inputConfig->objectKey = $this->input_file_path;
            //$inputConfig->host      = 's3-eu-west-1.amazonaws.com';      // OPTIONAL
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

            /* CREATE JOB CONFIG */
            $jobConfig = new JobConfig();
            $jobConfig->speed = JobSpeedTypes::STANDARD;
            $jobConfig->encodingProfile = $encodingProfile;
            $jobConfig->input = $input;
            $jobConfig->manifestTypes[] = ManifestTypes::M3U8;
            $jobConfig->manifestTypes[] = ManifestTypes::MPD;

            if('on' == $this->drm)
            {
                /* CREATE COMBINED WIDEVINE PLAYREADY DRM CONFIG */
                $combinedWidevinePlayreadyDRMConfig = new CombinedWidevinePlayreadyDRMConfig();
                $combinedWidevinePlayreadyDRMConfig->pssh = 'CAESEInw6s5KklokhmC6SPmlToEaCG1vdmlkb25lIhD97F4BwZf9R4oVfquQm4fhMgA=';
                $combinedWidevinePlayreadyDRMConfig->key = '8v4wly9BinkBrDdYIEnszQ==';
                $combinedWidevinePlayreadyDRMConfig->kid = '8OUM3TRiVymH5WXej9u0Ug==';
                $combinedWidevinePlayreadyDRMConfig->laUrl = 'http://playready.ezdrm.com/cency/preauth.aspx?pX=0FF54D';
                $combinedWidevinePlayreadyDRMConfig->method = DRMEncryptionMethods::MPEG_CENC;

//                /* CREATE DRM WIDEVINE CONFIG */
//                $widevineDRMConfig = new WidevineDRMConfig();
//                $widevineDRMConfig->requestUrl = 'http://license.uat.widevine.com/cenc/getcontentkey';
//                $widevineDRMConfig->signingKey = '1ae8ccd0e7985cc0b6203a55855a1034afc252980e970ca90e5202689f947ab9';
//                $widevineDRMConfig->signingIV = 'd58ce954203b7c9a9a9d467f59839249';
//                $widevineDRMConfig->contentId = '746573745f69645f4639465043304e4f';
//                $widevineDRMConfig->provider = 'widevine_test';
//                $widevineDRMConfig->method = DRMEncryptionMethods::MPEG_CENC;

                $jobConfig->drmConfig = $combinedWidevinePlayreadyDRMConfig;
            }


            /* CREATE JOB */
            $job = Job::create($jobConfig);

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



            $passThrough = str_replace("'",'"',json_encode(array('accountID'=>$this->accountID,'filmID'=>$id,'date'=>$this->dt,'locale'=>$params["locale"],'track'=>$params["track"],'media'=>$this->media,'quality'=>$this->quality,'BASEPATH'=>$this->outdir."/","JOB"=>$job->jobId,"INPUT"=>array('bucket'=>$this->input_bucket,'path'=>$this->input_path))));
            G('DB')->query("INSERT INTO z_pass_through (pass_through) VALUES ('$passThrough')");
            $passid = G('DB')->lastInsertId();

            G('DB')->query("INSERT INTO z_bitjobs (accounts_id,films_id,job_id,job_status,pass_id,dt,users_id) VALUES ('$this->accountID','$id','$job->jobId','$job->status','$passid',NOW(),'{$this->userID}')");
            return json_encode(array('status'=>'Media uploaded successfully, proceeding to transcoder'));
        } catch (Exception $e) { return json_encode(array('status'=>$e->getMessage(),'error'=>true)); }
    }


    public function createRegularJob($params)
    {
        $this->quality = $params["quality"]?$params["quality"]:"hd";
        $this->media = $params["media"];
        $id = ltrim($params["id"],'0');
        $this->dt = $params["dt"];

        $this->file = "MASTER.".$this->media.".".$id.".mp4";
        $this->dir = $this->media."s/".$this->dt."/".(str_pad($id,5,'0',STR_PAD_LEFT))."/".$params["track"]."/".$params["locale"]."/";
        $fileNameEndPart = (str_pad($id,5,'0',STR_PAD_LEFT)).".".$params["track"].".".$params["locale"]."";
        $this->input_bucket = $params["bucket"];
        $this->output_bucket = "us.cinecliq.".$this->media."s";

        try
        {
            $public = ($this->media == 'film'?'':'"public": 1,');
            $infile = $this->file;
            // Initialize the Services_Zencoder class
            $passThrough = str_replace('"',"'",json_encode(array('accountID'=>$this->accountID,'filmID'=>$id,'date'=>$this->dt,'locale'=>$params["locale"],'track'=>$params["track"],'media'=>$this->media,'quality'=>$this->quality)));

            $job_input ='
            "api_key": "4c15b5bbe4bc62d6851c04d84db71691",
            "input": "http://'.$this->input_bucket.'.s3.amazonaws.com/'.$this->media.'/'.$this->dt.'/'.(str_pad($id,5,'0',STR_PAD_LEFT)).'/'.$params["track"].'/'.$params["locale"].'/'.$infile.'",
            "region": "us",
            "download_connections": 5,
            "private": true,
            "pass_through":"'.$passThrough.'",
            "notifications": [{
                        "url": "http://prodev.cinehost.com/zen-notify-akamai.v4.php",
                        "format": "json"
                        }],
            "grouping": "Some group",';




            $job_out_common = '
                "credentials": "akamaissh2",
                "strict": true,
                "format": "mp4",
                "video_codec": "h264",
                "audio_codec": "aac",
                "aspect_mode": "preserve",
                "upscale": true,
                "quality" : 5,
                "audio_normalize": true,
                "audio_quality": 5,
                "speed": 1,
                "audio_channels": 2,
                "rotate": 0,
                '.$public.'
                "deinterlace": "off"
            ';





            if($this->quality == "hd") {
                $job_out[] = '{
                "label": "1920",
                "width": 1920,
                "url": "'.$this->urlprefix.'/215161/'.$this->dir.'1920.'.$fileNameEndPart.'.mp4",
                "base_url": "'.$this->urlprefix.'/",
                "filename": "1920.'.$fileNameEndPart.'.mp4",
                "max_video_bitrate": 4800,
                "h264_profile": "high",
                "h264_level": 4.1,
                '.$job_out_common.'
                }';
            }


            $job_out[] ='{
            "label": "1280",
            "width": 1280,
            "url": "'.$this->urlprefix.'/215161/'.$this->dir.'1280.'.$fileNameEndPart.'.mp4",
            "base_url": "'.$this->urlprefix.'/",
            "filename": "1280.'.$fileNameEndPart.'.mp4",
            "max_video_bitrate": 2800,
            "h264_profile": "baseline",
            "h264_level": 3,
            '.$job_out_common.'
            }';

            $job_out[] = '{
            "label": "960",
            "width": 960,
            "url": "'.$this->urlprefix.'/215161/'.$this->dir.'960.'.$fileNameEndPart.'.mp4",
            "base_url": "'.$this->urlprefix.'/",
            "filename": "960.'.$fileNameEndPart.'.mp4",
            "max_video_bitrate": 1800,
            "h264_profile": "baseline",
            "h264_level": 3,
            '.$job_out_common.'
            }';

            $job_out[] = '{
            "label": "640",
            "width": 640,
            "url": "'.$this->urlprefix.'/215161/'.$this->dir.'640.'.$fileNameEndPart.'.mp4",
            "base_url": "'.$this->urlprefix.'/",
            "filename": "640.'.$fileNameEndPart.'.mp4",
            "max_video_bitrate": 800,
            "h264_profile": "baseline",
            "h264_level": 3,
            '.$job_out_common.'
            }';

            $job_out[] = '{
                    "type": "playlist",
                    "url": "'.$this->urlprefix.'/215161/'.$this->dir.''.$fileNameEndPart.'.m3u8",
                    "base_url": "'.$this->urlprefix.'/",
                    "filename": "'.$fileNameEndPart.'.m3u8",
                    "streams": [
                        {
                            "bandwidth": 4800,
                            "path": "1920.'.$fileNameEndPart.'.mp4"
                        },
                        {
                            "bandwidth": 2800,
                            "path": "1280.'.$fileNameEndPart.'.mp4"
                        },
                        {
                            "bandwidth": 1800,
                            "path": "960.'.$fileNameEndPart.'.mp4"
                        },
                        {
                            "bandwidth": 800,
                            "path": "640.'.$fileNameEndPart.'.mp4"
                        }
                    ],
                    "credentials": "akamaissh2",
                    "strict": true,
                    "public": 1
                }';





            $job = '{'.$job_input.'"output": ['.(implode(',',$job_out)).']}';


            echo $job;

            $this->zencoder->jobs->create($job);
        } catch (Services_Zencoder_Exception $e) { }
    }
    public function createDashJob($params)
    {
        $this->quality = $params["quality"]?$params["quality"]:"hd";
        $this->media = $params["media"];
        $id = ltrim($params["id"],'0');
        $this->dt = $params["dt"];

        $this->file = "MASTER.".$this->media.".".$id.".mp4";
        $this->dir = $this->media."s/".$this->dt."/".(str_pad($id,5,'0',STR_PAD_LEFT))."/".$params["track"]."/".$params["locale"]."/";
        $fileNameEndPart = (str_pad($id,5,'0',STR_PAD_LEFT)).".".$params["track"].".".$params["locale"]."";
        $this->input_bucket = $params["bucket"];
        $this->output_bucket = "cinehost.streamer";
        $levels = $this->levels($this->quality);
        $outdir = $this->media.'/'.(implode('/',str_split($id)))."/".$params["locale"]."/".$params["track"];

        try
        {
            $public = ($this->media == 'film'?'':'"public": 1,');
            $infile = $this->file;
            // Initialize the Services_Zencoder class

            $outurls = array();


            foreach($levels as $br=>$v)
            {
                $job_out[] = '
                {
                  "headers": {"x-amz-acl": "public-read"},
                  "label": "mp4-' . $br . '",
                  "prepare_for_segmenting": ["hls", "dash"],
                  "video_bitrate": ' . $br . ',
                  "upscale": false,
                  "audio_normalize": true,
                  "audio_quality": 5,
                  "audio_codec": "aac",
                  "aspect_mode": "preserve",
                  "deinterlace": "off",
                  '.$public.'
                  "speed": 1,
                  "width": ' . $v['width'] . ',
                  "audio_channels": 2,
                  "decoder_bitrate_cap": ' . ceil($br * 0.9 - 128) . ',
                  "decoder_buffer_size": ' . ceil(($br * 0.9 - 128) * 1.5) . ',
                  "video_bitrate": ' . ceil(($br * 0.9 - 128) * 0.9) . ',
                  "url": "s3://'.$this->output_bucket.'.s3.amazonaws.com/'.$outdir.'/mp4/' . $br . '.mp4"
                }';

                $outurls['mpeg']['files'][]="s3://s3.amazonaws.com/{$this->output_bucket}/{$outdir}/mp4/{$br}.mp4";
            }
            foreach($levels as $br=>$v)
            {
                $job_out[] = '
                {
                  "headers": {"x-amz-acl": "public-read"},
                  "source": "mp4-'.$br.'",
                  "copy_video": true,
                  "copy_audio": true,
                  "type": "segmented",
                  "url": "s3://'.$this->output_bucket.'.s3.amazonaws.com/'.$outdir.'/hls/'.$br.'.m3u8"
                }';

                $outurls['hls']['files'][]="s3://s3.amazonaws.com/{$this->output_bucket}/{$outdir}/hls/{$br}.m3u8";


                $job_out[] = '
                {
                  "headers": {"x-amz-acl": "public-read"},
                  "source": "mp4-'.$br.'",
                  "label": "dash-'.$br.'",
                  "streaming_delivery_format": "dash",
                  "copy_video": true,
                  "copy_audio": true,
                  "type": "segmented",
                  "url": "s3://'.$this->output_bucket.'.s3.amazonaws.com/'.$outdir.'/dash/'.$br.'k/rendition.mpd"
                }';

                $outurls['dash']['files'][]="s3://s3.amazonaws.com/{$this->output_bucket}/{$outdir}/dash/{$br}k/rendition.mpd";

                $playlistDASH[] = '{ "source": "dash-'.$br.'", "path": "'.$br.'k" }';
                $playlistHLS[] = '{ "path": "'.$br.'.m3u8", "bandwidth": '.$br.' }';
            }

            $job_out[] = '
            {
              "headers": {"x-amz-acl": "public-read"},
              "type": "playlist",
              "url": "s3://'.$this->output_bucket.'.s3.amazonaws.com/'.$outdir.'/hls/playlist.m3u8",
              "streams": [
                '.(implode(',',$playlistHLS)).'
              ]
            }';

            $outurls['hls']['playlist']="s3://s3.amazonaws.com/{$this->output_bucket}/{$outdir}/hls/playlist.m3u8";

            $job_out[] = '
            {
              "headers": {"x-amz-acl": "public-read"},
              "streaming_delivery_format": "dash",
              "type": "playlist",
              "url": "s3://'.$this->output_bucket.'.s3.amazonaws.com/'.$outdir.'/dash/playlist.mpd",
              "streams": [
                '.(implode(',',$playlistDASH)).'
              ]
            }';

            $outurls['dash']['playlist']="s3://s3.amazonaws.com/{$this->output_bucket}/{$outdir}/dash/playlist.mpd";



            if($params['file']) // custom deployment via harddrives
            {
                $passThrough = str_replace("'",'"',json_encode(array('accountID'=>$this->accountID,'filmID'=>$id,'date'=>$this->dt,'locale'=>$params["locale"],'track'=>$params["track"],'media'=>$this->media,'quality'=>$this->quality,'BASEPATH'=>$outdir."/","OUTPUT"=>$outurls,"INPUT"=>array('bucket'=>$this->input_bucket,'path'=>$params["file"]))));
            }
            else{
                $passThrough = str_replace("'",'"',json_encode(array('accountID'=>$this->accountID,'filmID'=>$id,'date'=>$this->dt,'locale'=>$params["locale"],'track'=>$params["track"],'media'=>$this->media,'quality'=>$this->quality,'BASEPATH'=>$outdir."/","OUTPUT"=>$outurls,"INPUT"=>array('bucket'=>$this->input_bucket,'path'=>$this->media.'/'.$this->dt.'/'.(str_pad($id,5,'0',STR_PAD_LEFT)).'/'.$params["track"].'/'.$params["locale"].'/'))));
            }

            G('DB')->query("INSERT INTO z_pass_through (pass_through) VALUES ('$passThrough')");
            $passid = G('DB')->lastInsertId();

            $passThrough = str_replace('"',"'",json_encode(array('accountID'=>$this->accountID,'filmID'=>$id,'date'=>$this->dt,'locale'=>$params["locale"],'track'=>$params["track"],'media'=>$this->media,'quality'=>$this->quality,'passid'=>$passid)));

            $inputfile = $params['file']?'http://'.$this->input_bucket.'.s3.amazonaws.com'.$params["file"]:'http://'.$this->input_bucket.'.s3.amazonaws.com/'.$this->media.'/'.$this->dt.'/'.(str_pad($id,5,'0',STR_PAD_LEFT)).'/'.$params["track"].'/'.$params["locale"].'/'.$infile;

            $job_input ='
            "api_key": "4c15b5bbe4bc62d6851c04d84db71691",
            "input": "'.$inputfile.'",
            "region": "us",
            "download_connections": 5,
            "private": true,
            "pass_through":"'.$passThrough.'",
            "notifications": [{
                        "url": "http://prodev.cinehost.com/zen-notify-dash.v5.php",
                        "format": "json"
                        }],
            "grouping": "Some group",';


            $job = '{'.$job_input.'"output": ['.(implode(',',$job_out)).']}';
            echo $job;
            $this->zencoder->jobs->create($job);

        } catch (Services_Zencoder_Exception $e) { }
    }
    public function createDashDVDJob($params)
    {
        $this->quality = $params["quality"]?$params["quality"]:"hd";
        $this->media = $params["media"];
        $id = ltrim($params["id"],'0');
        $this->dt = $params["dt"];

        $this->file = "MASTER.".$this->media.".".$id.".mp4";
        $this->dir = $this->media."s/".$this->dt."/".(str_pad($id,5,'0',STR_PAD_LEFT))."/".$params["track"]."/".$params["locale"]."/";
        $fileNameEndPart = (str_pad($id,5,'0',STR_PAD_LEFT)).".".$params["track"].".".$params["locale"]."";
        $this->input_bucket = $params["bucket"];
        $this->output_bucket = "cinehost.streamer";
        $levels = $this->levels($this->quality);
        $outdir = $this->media.'/'.(implode('/',str_split($id)))."/".$params["locale"]."/".$params["track"];

        try
        {
            $public = ($this->media == 'film'?'':'"public": 1,');
            $infile = $this->file;
            // Initialize the Services_Zencoder class

            $outurls = array();


            foreach($levels as $br=>$v)
            {
                $job_out[] = '
                {
                  "headers": {"x-amz-acl": "public-read"},
                  "label": "mp4-' . $br . '",
                  "prepare_for_segmenting": ["hls", "dash"],
                  "video_bitrate": ' . $br . ',
                  "upscale": false,
                  "audio_normalize": true,
                  "audio_quality": 5,
                  "audio_codec": "aac",
                  "aspect_mode": "preserve",
                  "deinterlace": "off",
                  '.$public.'
                  "speed": 1,
                  "width": ' . $v['width'] . ',
                  "audio_channels": 2,
                  "decoder_bitrate_cap": ' . ceil($br * 0.9 - 128) . ',
                  "decoder_buffer_size": ' . ceil(($br * 0.9 - 128) * 1.5) . ',
                  "video_bitrate": ' . ceil(($br * 0.9 - 128) * 0.9) . ',
                  "url": "s3://'.$this->output_bucket.'.s3.amazonaws.com/'.$outdir.'/mp4/' . $br . '.mp4"
                }';

                $outurls['mpeg']['files'][]="s3://s3.amazonaws.com/{$this->output_bucket}/{$outdir}/mp4/{$br}.mp4";
            }

            if($params['file']) // custom deployment via harddrives
            {
                $passThrough = str_replace("'",'"',json_encode(array('accountID'=>$this->accountID,'filmID'=>$id,'date'=>$this->dt,'locale'=>$params["locale"],'track'=>$params["track"],'media'=>$this->media,'quality'=>$this->quality,'BASEPATH'=>$outdir."/","OUTPUT"=>$outurls,"INPUT"=>array('bucket'=>$this->input_bucket,'path'=>$params["file"]))));
            }
            else{
                $passThrough = str_replace("'",'"',json_encode(array('accountID'=>$this->accountID,'filmID'=>$id,'date'=>$this->dt,'locale'=>$params["locale"],'track'=>$params["track"],'media'=>$this->media,'quality'=>$this->quality,'BASEPATH'=>$outdir."/","OUTPUT"=>$outurls,"INPUT"=>array('bucket'=>$this->input_bucket,'path'=>$this->media.'/'.$this->dt.'/'.(str_pad($id,5,'0',STR_PAD_LEFT)).'/'.$params["track"].'/'.$params["locale"].'/'))));
            }

            G('DB')->query("INSERT INTO z_pass_through (pass_through) VALUES ('$passThrough')");
            $passid = G('DB')->lastInsertId();

            $passThrough = str_replace('"',"'",json_encode(array('accountID'=>$this->accountID,'filmID'=>$id,'date'=>$this->dt,'locale'=>$params["locale"],'track'=>$params["track"],'media'=>$this->media,'quality'=>$this->quality,'passid'=>$passid)));

            $inputfile = $params['file']?'http://'.$this->input_bucket.'.s3.amazonaws.com'.$params["file"]:'http://'.$this->input_bucket.'.s3.amazonaws.com/'.$this->media.'/'.$this->dt.'/'.(str_pad($id,5,'0',STR_PAD_LEFT)).'/'.$params["track"].'/'.$params["locale"].'/'.$infile;

            $job_input ='
            "api_key": "4c15b5bbe4bc62d6851c04d84db71691",
            "input": "'.$inputfile.'",
            "region": "us",
            "download_connections": 5,
            "private": true,
            "pass_through":"'.$passThrough.'",
            "notifications": [{
                        "url": "http://prodev.cinehost.com/zen-notify-dash.v5.php",
                        "format": "json"
                        }],
            "grouping": "Some group",';


            $job = '{'.$job_input.'"output": ['.(implode(',',$job_out)).']}';
            echo $job;
            $this->zencoder->jobs->create($job);

        } catch (Services_Zencoder_Exception $e) { }
    }

} 
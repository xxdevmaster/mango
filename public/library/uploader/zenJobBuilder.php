<?php
/**
 * Created by PhpStorm.
 * User: edgar
 * Date: 3/10/15
 * Time: 3:21 PM
 */
require dirname(__FILE__).'/../../vendor/autoload.php';

class zenJobBuilder {
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

    public function __construct($accountID){
        $this->accountID = $accountID;
        $this->zencoder = new Services_Zencoder('4c15b5bbe4bc62d6851c04d84db71691');

    }

    public function getJobs(){
        return $this->zencoder->jobs->index();
    }


    public function createJob($params)
    {
        $exceptions = array('69', '227', '72', '19');
        if(in_array($this->accountID, $exceptions))
            if($params["quality"] == 'offline')
                $this->createDashDVDJob($params);
            else
                $this->createDashJob($params);
        else
            $this->createRegularJob($params);
    }

    private function levels($quality)
    {
        $levels = array(
            '400'=>array('width'=>480),
            '800'=>array('width'=>640),
            //'1250'=>array('width'=>768),
            '1200'=>array('width'=>960),
            '2400'=>array('width'=>1280),
            //'2800'=>array('width'=>1280),
        );
        if($quality == 'hd')
            $levels['4600'] = array('width'=>1920);

        if($quality == 'offline')
            $levels = array('4600' => array('width'=>1920));

        return $levels;
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



            if($params['full_path']) // custom deployment via harddrives
            {
                $passThrough = str_replace("'",'"',json_encode(array('accountID'=>$this->accountID,'filmID'=>$id,'date'=>$this->dt,'locale'=>$params["locale"],'track'=>$params["track"],'media'=>$this->media,'quality'=>$this->quality,'BASEPATH'=>$outdir."/","OUTPUT"=>$outurls,"INPUT"=>array('bucket'=>$this->input_bucket,'path'=>$params["full_path"]))));
            }
            else{
                $passThrough = str_replace("'",'"',json_encode(array('accountID'=>$this->accountID,'filmID'=>$id,'date'=>$this->dt,'locale'=>$params["locale"],'track'=>$params["track"],'media'=>$this->media,'quality'=>$this->quality,'BASEPATH'=>$outdir."/","OUTPUT"=>$outurls,"INPUT"=>array('bucket'=>$this->input_bucket,'path'=>$this->media.'/'.$this->dt.'/'.(str_pad($id,5,'0',STR_PAD_LEFT)).'/'.$params["track"].'/'.$params["locale"].'/'))));
            }

            G('DB')->query("INSERT INTO z_pass_through (pass_through) VALUES ('$passThrough')");
            $passid = G('DB')->lastInsertId();

            $passThrough = str_replace('"',"'",json_encode(array('accountID'=>$this->accountID,'filmID'=>$id,'date'=>$this->dt,'locale'=>$params["locale"],'track'=>$params["track"],'media'=>$this->media,'quality'=>$this->quality,'passid'=>$passid)));

            $inputfile = $params['full_path']?'http://'.$this->input_bucket.'.s3.amazonaws.com'.$params["full_path"]:'http://'.$this->input_bucket.'.s3.amazonaws.com/'.$this->media.'/'.$this->dt.'/'.(str_pad($id,5,'0',STR_PAD_LEFT)).'/'.$params["track"].'/'.$params["locale"].'/'.$infile;

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
            //echo $job;
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

            if($params['full_path']) // custom deployment via harddrives
            {
                $passThrough = str_replace("'",'"',json_encode(array('accountID'=>$this->accountID,'filmID'=>$id,'date'=>$this->dt,'locale'=>$params["locale"],'track'=>$params["track"],'media'=>$this->media,'quality'=>$this->quality,'BASEPATH'=>$outdir."/","OUTPUT"=>$outurls,"INPUT"=>array('bucket'=>$this->input_bucket,'path'=>$params["full_path"]))));
            }
            else{
                $passThrough = str_replace("'",'"',json_encode(array('accountID'=>$this->accountID,'filmID'=>$id,'date'=>$this->dt,'locale'=>$params["locale"],'track'=>$params["track"],'media'=>$this->media,'quality'=>$this->quality,'BASEPATH'=>$outdir."/","OUTPUT"=>$outurls,"INPUT"=>array('bucket'=>$this->input_bucket,'path'=>$this->media.'/'.$this->dt.'/'.(str_pad($id,5,'0',STR_PAD_LEFT)).'/'.$params["track"].'/'.$params["locale"].'/'))));
            }

            G('DB')->query("INSERT INTO z_pass_through (pass_through) VALUES ('$passThrough')");
            $passid = G('DB')->lastInsertId();

            $passThrough = str_replace('"',"'",json_encode(array('accountID'=>$this->accountID,'filmID'=>$id,'date'=>$this->dt,'locale'=>$params["locale"],'track'=>$params["track"],'media'=>$this->media,'quality'=>$this->quality,'passid'=>$passid)));

            $inputfile = $params['full_path']?'http://'.$this->input_bucket.'.s3.amazonaws.com'.$params["full_path"]:'http://'.$this->input_bucket.'.s3.amazonaws.com/'.$this->media.'/'.$this->dt.'/'.(str_pad($id,5,'0',STR_PAD_LEFT)).'/'.$params["track"].'/'.$params["locale"].'/'.$infile;

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
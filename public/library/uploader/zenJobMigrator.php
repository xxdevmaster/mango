<?php
/**
 * Created by PhpStorm.
 * User: edgar
 * Date: 3/10/15
 * Time: 3:21 PM
 */
require dirname(__FILE__).'/../../vendor/autoload.php';

class zenJobMigrator {
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
        $this->zencoder = new Services_Zencoder('4c15b5bbe4bc62d6851c04d84db71691');

    }

    public function getJobs(){
        return $this->zencoder->jobs->index();
    }


    public function createJob($params)
    {
        $this->createDashMigrateJob($params);
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
        return $levels;
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
        //$this->file = basename($params["path"]);//"MASTER.".$this->media.".".$id.".mp4";
        //$this->dir = "{$this->media}s/{$this->dt}/".(str_pad($id,5,'0',STR_PAD_LEFT))."/{$this->track}/{$this->locale}/";
        $this->output_bucket = "cinehost.streamer";
        $levels = $this->levels($this->quality);
        $this->outdir = "{$this->media}/".(implode('/',str_split((str_pad($id,5,'0',STR_PAD_LEFT)))))."/{$this->locale}/{$this->track}";

        try
        {
            $public = ($this->media == 'film'?'':'"public": 1,');
            //$infile = $this->file;
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
                  "url": "s3://'.$this->output_bucket.'.s3.amazonaws.com/'.$this->outdir.'/mp4/' . $br . '.mp4"
                }';

                $outurls['mpeg']['files'][]="s3://{$this->output_bucket}.s3.amazonaws.com/{$this->outdir}/mp4/{$br}.mp4";
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
                  "url": "s3://'.$this->output_bucket.'.s3.amazonaws.com/'.$this->outdir.'/hls/'.$br.'.m3u8"
                }';

                $outurls['hls']['files'][]="s3://{$this->output_bucket}.s3.amazonaws.com/{$this->outdir}/hls/{$br}.m3u8";


                $job_out[] = '
                {
                  "headers": {"x-amz-acl": "public-read"},
                  "source": "mp4-'.$br.'",
                  "label": "dash-'.$br.'",
                  "streaming_delivery_format": "dash",
                  "copy_video": true,
                  "copy_audio": true,
                  "type": "segmented",
                  "url": "s3://'.$this->output_bucket.'.s3.amazonaws.com/'.$this->outdir.'/dash/'.$br.'k/rendition.mpd"
                }';

                $outurls['dash']['files'][]="s3://{$this->output_bucket}.s3.amazonaws.com/{$this->outdir}/dash/{$br}k/rendition.mpd";

                $playlistDASH[] = '{ "source": "dash-'.$br.'", "path": "'.$br.'k" }';
                $playlistHLS[] = '{ "path": "'.$br.'.m3u8", "bandwidth": '.$br.' }';
            }

            $job_out[] = '
            {
              "headers": {"x-amz-acl": "public-read"},
              "type": "playlist",
              "url": "s3://'.$this->output_bucket.'.s3.amazonaws.com/'.$this->outdir.'/hls/playlist.m3u8",
              "streams": [
                '.(implode(',',$playlistHLS)).'
              ]
            }';

            $outurls['hls']['playlist']="s3://{$this->output_bucket}.s3.amazonaws.com/{$this->outdir}/hls/playlist.m3u8";

            $job_out[] = '
            {
              "headers": {"x-amz-acl": "public-read"},
              "streaming_delivery_format": "dash",
              "type": "playlist",
              "url": "s3://'.$this->output_bucket.'.s3.amazonaws.com/'.$this->outdir.'/dash/playlist.mpd",
              "streams": [
                '.(implode(',',$playlistDASH)).'
              ]
            }';

            $outurls['dash']['playlist']="s3://{$this->output_bucket}.s3.amazonaws.com/{$this->outdir}/dash/playlist.mpd";



            $passThrough = str_replace("'",'"',json_encode(array('accountID'=>$this->accountID,'filmID'=>$id,'date'=>$this->dt,'locale'=>$params["locale"],'track'=>$params["track"],'media'=>$this->media,'quality'=>$this->quality,'BASEPATH'=>$this->outdir."/","OUTPUT"=>$outurls)));
            G('DB')->query("INSERT INTO z_pass_through (pass_through) VALUES ('$passThrough')");
            $passid = G('DB')->lastInsertId();

            $passThrough = str_replace('"',"'",json_encode(array('accountID'=>$this->accountID,'filmID'=>$id,'date'=>$this->dt,'locale'=>$params["locale"],'track'=>$params["track"],'media'=>$this->media,'quality'=>$this->quality,'passid'=>$passid)));

            $inputfile = "sftp://demandcliq.upload.akamai.com/215161/".$this->path;


            //$params['file']?'http://'.$this->input_bucket.'.s3.amazonaws.com'.$params["file"]:'http://'.$this->input_bucket.'.s3.amazonaws.com/'.$this->media.'/'.$this->dt.'/'.(str_pad($id,5,'0',STR_PAD_LEFT)).'/'.$params["track"].'/'.$params["locale"].'/'.$infile;

            $job_input ='
            "api_key": "4c15b5bbe4bc62d6851c04d84db71691",
            "credentials": "akamaissh2",
            "input": "'.$inputfile.'",
            "region": "us",
            "download_connections": 5,
            "private": true,
            "pass_through":"'.$passThrough.'",
            "notifications": [{
                        "url": "http://kiwi.cinehost.com/zen-notify-dash-migrate.v5.php",
                        "format": "json"
                        }],
            "grouping": "Some group",';


            $job = '{'.$job_input.'"output": ['.(implode(',',$job_out)).']}';
            //echo $job;
            $this->zencoder->jobs->create($job);

        } catch (Services_Zencoder_Exception $e) { }
    }

} 
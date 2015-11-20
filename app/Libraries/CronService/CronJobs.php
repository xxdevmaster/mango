<?php
namespace App\Libraries\CronService;

require_once dirname(__FILE__).'/../../vendor/autoload.php';
use bitcodin\AwsRegion;
use bitcodin\Bitcodin;
use bitcodin\Job;
use bitcodin\Output;
use bitcodin\S3OutputConfig;

class CronJobs {
    private static $initialized = false;
    private static function initialize(){
        if (self::$initialized)
                return;
        self::$initialized = true;
    }

    public static function updatePrices()
    {
        $raw = file_get_contents("http://www.oanda.com/embedded/converter/show/b2FuZGFlY2N1c2VyLy9kZWZhdWx0/0/ru/");
        preg_match("/var rates = (.+)</",$raw,$m);
        $rates = json_decode($m[1]);
        if(!is_object($rates) or !is_array($rates->askRates) or !is_array($rates->bidRates) or !is_array($rates->pairs))
            exit;

        echo date("Y-m-d H:i").": ".$m[1]."\r\n";

        $ratelist = array();
        $sqlz = array();
        foreach($rates->pairs as $id=>$pair)
        {
            $parts = explode('/',$pair);
            if('USD' == $parts[0])
            {
                $key = $parts[1];
                $val = $rates->askRates[$id];
            }
            else if ('USD' == $parts[1])
            {
                $key = $parts[0];
                $revrate = $rates->bidRates[$id]?$rates->bidRates[$id]:$rates->askRates[$id];
                $val = 1/$revrate;
            }
            else
                continue;

            $ratelist[$key]=$val;
            $sqlz[] = "UPDATE cc_geo_contracts SET rent_price=(rent_price_national/$val) WHERE rent_price_national>0 AND countries_id IN (SELECT id FROM cc_countries WHERE currency_code='$key')";
            $sqlz[] = "UPDATE cc_geo_contracts SET buy_price=(buy_price_national/$val) WHERE buy_price_national>0 AND countries_id IN (SELECT id FROM cc_countries WHERE currency_code='$key')";
            //    echo $pair.'<br>';
        }
        $sqlz[] = "UPDATE cc_geo_contracts SET rent_price=rent_price_nominal WHERE rent_price_national=0";
        $sqlz[] = "UPDATE cc_geo_contracts SET buy_price=buy_price_nominal WHERE buy_price_national=0";

// Memcache
        if(!$M)
        {
            $M = new Memcache;
            $M->connect('localhost', 11211) or die ("Could not connect");
        }
        try {
            $M->set('currency_rates', $ratelist, false, 3600); // storing friends in cache for 1 hour
        } catch(Exception $err){}


        G('DB')->exec("SET AUTOCOMMIT=0");
        G('DB')->exec("BEGIN");
        foreach($sqlz as $sql)
            G('DB')->exec($sql);
        G('DB')->exec("COMMIT");
        G('DB')->exec("SET AUTOCOMMIT=1");
    }


        public static function updateBitcodinJobs() {
        Bitcodin::setApiToken('0c96481e564313c6519102a323e9aacfe33cd84e93b9a8d93bb680bace598475');
        $q = "SELECT j.*,p.pass_through FROM z_bitjobs j,z_pass_through p WHERE j.pass_id=p.id AND j.job_status NOT IN ('Success','Error')";
        $res= G('DB')->query($q);
        while($row = $res->fetch(PDO::FETCH_OBJ))
        {
            /* LOAD JOB */
            $job = Job::get($row->job_id);
            $status = $job->status;
            $pass = json_decode(str_replace("'",'"',$row->pass_through));
            echo $status;
            if('Transfering' == $row->job_status)
            {
                if('Finished' == $status) {
                    G('DB')->query("UPDATE z_bitjobs SET job_status='Pending' WHERE id='{$row->id}'");
                    echo (file_get_contents("http://kiwi.cinehost.com/bit-notify-dash.v1.php?job_id={$row->job_id}"));
                }
            }
            else
            {
                G('DB')->query("UPDATE z_bitjobs SET job_status='{$status}' WHERE id='{$row->id}'");
                if('Finished' == $status)
                {
                    G('DB')->query("UPDATE z_bitjobs SET job_status='Transfering' WHERE id='{$row->id}'");
                    $pass = json_decode(str_replace("'",'"',$row->pass_through));

                    try {
                        $outputConfig = new S3OutputConfig();
                        $outputConfig->name = "CinehostS3Output";
                        $outputConfig->accessKey = "AKIAJPIY5AB3KDVIDPOQ";
                        $outputConfig->secretKey = "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";
                        $outputConfig->bucket = "cinehost.streamer";
                        $outputConfig->region = AwsRegion::US_EAST_1;
                        $outputConfig->prefix = $pass->BASEPATH;
                        $outputConfig->makePublic = true;
                        $outputConfig->host = "s3-us-east-1.amazonaws.com";
                        $output = Output::create($outputConfig);

                        $jobTransfer = $job->transfer($output);

                        // write outputs
                        preg_match("/.+\/(.+)\/(.+).mpd/",$job->manifestUrls->mpdUrl,$matches_mpd);
                        preg_match("/.+\/(.+)\/(.+).m3u8/",$job->manifestUrls->m3u8Url,$matches_m3u8);
                        $outurls['dash']['playlist']="https://s3.amazonaws.com/cinehost.streamer/{$pass->BASEPATH}{$matches_mpd[1]}/{$matches_mpd[2]}.mpd";
                        $outurls['hls']['playlist']="https://s3.amazonaws.com/cinehost.streamer/{$pass->BASEPATH}{$matches_m3u8[1]}/{$matches_m3u8[2]}.m3u8";
                        $pass->OUTPUT = $outurls;
                        $passThrough = str_replace("'",'"',json_encode($pass));
                        G('DB')->query("UPDATE z_pass_through SET pass_through='{$passThrough}' WHERE id='{$row->pass_id}'");

                    }catch(Exception $e){
                        echo $e->getMessage();
                    }
                }
            }


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
        }
    }

}
?>
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CronJobs extends Model
{
    protected $table = "cron_jobs";

    public $timestamps = false;

    protected $fillable = ['id', 'vaults_id', 'delete_dt', 'companies_id', 'films_id'];
}

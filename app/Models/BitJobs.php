<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BitJobs extends Model
{
    protected $table = "z_bitjobs";

    protected $fillable = ['id', 'accounts_id', 'films_id', 'job_id', 'job_status', 'pass_id', 'dt', 'users_id'];

    public $timestamps = false;

}

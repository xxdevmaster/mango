<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vcbunches extends Model
{
    protected $table = "cc_vcbunches";

    public $timestamps = false;

    protected $fillable = ['id', 'title', 'dt', 'vc_start', 'vc_end', 'dt_start', 'dt_end', 'enabled', 'deleted', 'sticky', 'channels_id'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zvcodes extends Model
{
    protected $table = "z_vcodes";

    public $timestamps = false;

    protected $fillable = [
        'id', 'code', 'dt_from', 'dt_end', 'used', 'dtused', 'vcbunches_id', 'owner_id', 'rel'
    ];
}

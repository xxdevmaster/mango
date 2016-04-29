<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bundles extends Model
{
    protected $table = "cc_bundles";

    protected $fillable = ['id', 'channels_id', 'title', 'duration', 'amount', 'deleted'];

    public $timestamps = false;
}

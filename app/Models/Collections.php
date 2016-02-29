<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collections extends Model
{
    protected $table = "cc_collections";
	
    public $timestamps = false;
	
	protected $fillable = ['id', 'title', 'deleted', 'active', 'priority', 'source', 'source_id', 'channels_id', 'device', 'locale'];
}

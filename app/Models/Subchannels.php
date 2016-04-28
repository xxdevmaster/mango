<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subchannels extends Model
{
    protected $table = "cc_subchannels";
	
    public $timestamps = false;
	
	protected $fillable = ['id', 'title', 'deleted', 'active', 'priority', 'source', 'source_id', 'channels_id', 'device', 'subscriptions_id', 'bundles_id', 'model', 'locale', 'parent_id'];
}

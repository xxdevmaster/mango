<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Silders extends Model
{
    protected $table = "cc_silders";
	
    public $timestamps = false;
	
	protected $fillable = ['id', 'title', 'channel_id'];
}

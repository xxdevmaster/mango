<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sliders extends Model
{
    protected $table = "cc_sliders";
	
    public $timestamps = false;
	
	protected $fillable = ['id', 'title', 'channel_id', 'width', 'height'];
}

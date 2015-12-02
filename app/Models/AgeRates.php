<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgeRates extends Model
{
	protected $table = "cc_age_rates";
   
	public $timestamps = false;
	
	protected $fillable = ['id', 'countries_id', 'title', 'logo', 'priority', 'deleted', 'code'];	
}

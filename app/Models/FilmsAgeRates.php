<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FilmsAgeRates extends Model
{
	protected $table = "fk_films_age_rates";
   
	public $timestamps = false;
	
	protected $fillable = ['id', 'films_id', 'age_rates_id'];	
}

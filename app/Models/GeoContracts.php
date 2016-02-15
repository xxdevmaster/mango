<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeoContracts extends Model
{
    protected $table = "cc_geo_contracts";
	
    public $timestamps = false;
	
	protected $fillable = [
		'id', 'films_id', 'bcontracts_id',
		'countries_id', 'companies_id', 'start_date',
		'end_date', 'loyality', 'rent_price', 'rent_price_national',
		'rent_price_nominal', 'buy_price', 'buy_price_national', 'buy_price_nominal', 
		'deleted', 'priority'
	];
}

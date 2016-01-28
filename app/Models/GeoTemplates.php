<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeoTemplates extends Model
{
    protected $table = "cc_geotemplates";
	
    public $timestamps = false;
	
	protected $fillable = ['id', 'title', 'position', 'deleted'];

    public function countries(){
        return $this->belongsToMany('App\Countries', 'fk_geotemplates_countries', 'countries_id', 'geotemplates_id');
    }
}

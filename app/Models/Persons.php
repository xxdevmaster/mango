<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persons extends Model
{
    protected $table = "cc_persons";
	
	protected $fillable = ['id', 'title', 'img', 'brief', 'deleted', 'tmdb_id', 'bdt', 'last_uopdated', 'placeofbirth'];	
	
    public $timestamps = false;
}

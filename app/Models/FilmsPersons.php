<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FilmsPersons extends Model
{
    protected $table = "fk_films_persons";
	
	protected $fillable = ['id', 'persons_id', 'films_id', 'jobs_id', 'priority'];	
	
    public $timestamps = false;
}

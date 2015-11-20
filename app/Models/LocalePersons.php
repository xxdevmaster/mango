<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocalePersons extends Model
{
    protected $table = "locale_persons";
	
    public $timestamps = false;
	
	protected $fillable = ['id', 'title', 'brief', 'locale', 'persons_id'];
}

<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class FilmsProdCompanies extends Model
{
   protected $table = "fk_films_prodcompanies";
	
   public $timestamps = false;

   protected $fillable = ['id', 'prodcompanies_id', 'films_id'];
}

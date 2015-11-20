<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class FilmsProdCompanies extends Model
{
   protected $table = "fk_films_prodcompanies";
   
   protected $primaryKey = "films_id";
	
   public $timestamps = false;

   protected $fillable = ['id', 'prodcompanies_id', 'films_id'];
}

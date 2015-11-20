<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    //
    protected $table = "cc_films";
	
   public $timestamps = false;
   
    public function companies(){
        return $this->belongsToMany('App\Company', 'fk_films_owners', 'films_id', 'owner_id');
    } 
	
    public function baseContract(){
        return $this->hasOne('App\BaseContracts', 'films_id');
    }
	
	public function genres(){
        return $this->belongsToMany('App\Genres', 'fk_films_genres', 'films_id', 'genres_id');
    }	
	
	public function languages(){
        return $this->belongsToMany('App\Languages', 'fk_films_languages', 'films_id', 'languages_id');
    }	
	
	public function prodCompanies(){
        return $this->belongsToMany('App\ProdCompanies', 'fk_films_prodcompanies', 'films_id', 'prodcompanies_id');
    }
	
	public function countries(){
        return $this->belongsToMany('App\Countries', 'fk_films_countries', 'films_id', 'countries_id');
    }

	public function persons(){
       return $this->belongsToMany('App\Models\Persons', 'fk_films_persons', 'films_id', 'persons_id')->join('cc_jobs', 'cc_jobs.id', '=', 'fk_films_persons.jobs_id')->select(array('cc_jobs.*', 'cc_jobs.title as jobs_title', 'cc_persons.*'));
    }

	public function jobs(){
        return $this->belongsToMany('App\Models\Jobs', 'fk_films_persons', 'films_id', 'jobs_id');
    }
}

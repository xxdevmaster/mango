<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    //
    protected $table = "cc_companies";
/*
    public function filmOwners(){
        return $this->hasMany('App\FilmOwners', 'owner_id');
    }*/


    public function films(){
        return $this->belongsToMany('App\Film', 'fk_films_owners', 'owner_id', 'films_id');
    }
    public function stores(){
        return $this->belongsToMany('App\Film', 'fk_films_owners', 'owner_id', 'films_id');
    }
}

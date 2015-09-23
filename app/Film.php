<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    //
    protected $table = "cc_films";


    public function companies(){
        return $this->belongsToMany('App\Company', 'fk_films_owners', 'films_id', 'owner_id');
    }

}

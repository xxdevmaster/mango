<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'cc_accounts';

    //
    public function users(){
        return $this->hasMany('App\User');
    }

    public function company(){
        return $this->belongsTo('App\Company', 'companies_id');
    }

    public function store(){
        return $this->belongsTo('App\Store', 'platforms_id');
    }




}

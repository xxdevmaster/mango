<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'cc_accounts';

    //
    public function users(){
        return $this->hasMany('App\User', 'accounts_id');
    }

    public function company(){
        return $this->belongsTo('App\Company', 'companies_id');
    }

    public function store(){
        return $this->belongsTo('App\Store', 'platforms_id');
    }

    public function features(){
        return $this->belongsToMany('App\Features', 'fk_accounts_features', 'accounts_id', 'features_id');
    }




}

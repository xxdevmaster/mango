<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Features extends Model
{
    //
    protected $table = 'cc_features';


    public function accFeatures(){
        return $this->hasMany('App\AccountFeatures', 'features_id');
    }
}

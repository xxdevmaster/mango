<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    //
    protected $table = "cc_channels";

    public function contracts(){
        return $this->belongsToMany('App\BaseContracts', 'cc_channels_contracts', 'channel_id', 'bcontracts_id');
    }

}

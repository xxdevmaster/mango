<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseContracts extends Model
{
    //
    protected $table = 'cc_base_contracts';

    public $timestamps = false;

    public function films(){
        return $this->belongsTo('App\Film', 'films_id');
    }

    public function stores(){
        return $this->belongsToMany('App\Store', 'cc_channels_contracts', 'bcontracts_id', 'channel_id');
    }

    public function channelsContracts(){
        return $this->hasOne('App\Models\ChannelsContracts', 'bcontracts_id');
    }
}

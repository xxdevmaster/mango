<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChannelsContracts extends Model
{
    protected $table = "cc_channels_contracts";

    public $timestamps = false;

    protected $fillable = ['id', 'channel_id', 'bcontracts_id', 'subscribable', 'film_status'];

    public function companies(){
        return $this->belongsToMany('App\FilmsOwners', 'cc_channels_contracts', 'channel_id', 'bcontracts_id');
    }

    public function geoContractsShares(){
        return $this->hasOne('App\Models\ContractsShares', 'contracts_id');
    }
}

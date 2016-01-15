<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChannelsContracts extends Model
{
    protected $table = "cc_channels_contracts";

    public $timestamps = false;

    protected $fillable = ['id', 'channel_id', 'bcontracts_id', 'subscribable', 'film_status'];
}

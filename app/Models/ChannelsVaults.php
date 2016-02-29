<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChannelsVaults extends Model
{
    protected $table = "fk_channels_vaults";

    public $timestamps = false;

    protected $fillable = ['id', 'vaults_id', 'channels_id'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubChannelsLocale extends Model
{
    protected $table = "locale_subchannels";

    public $timestamps = false;

    protected $fillable = [
        'id', 'subchannels_id', 'title', 'locale', 'def'
    ];
}

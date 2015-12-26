<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonusMaterials extends Model
{
    protected $table = "cc_bonusmaterials";

    protected $fillable = ['id', 'films_id', 'locale', 'bonus_mrss', 'def', 'deleted', 'track_index', 'free', 'splash', 'position'];

    public $timestamps = false;
}

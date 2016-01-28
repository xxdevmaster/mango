<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeoTemplatesCountries extends Model
{
    protected $table = "fk_geotemplates_countries";

    public $timestamps = false;

    protected $fillable = ['id', 'countries_id', 'geotemplates_id'];
}

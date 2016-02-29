<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FilmSlidersImages extends Model
{
   protected $table = "fk_sliders_images";

   public $timestamps = false;

   protected $fillable = ['id', 'title', 'brief', 'ext_url', 'filename', 'url', 'films_id', 'sliders_id', 'position', 'locale'];
}

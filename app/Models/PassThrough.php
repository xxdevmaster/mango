<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PassThrough extends Model
{
    protected $table = "z_pass_through";

    protected $fillable = ['id', 'pass_through'];

    public $timestamps = false;

}

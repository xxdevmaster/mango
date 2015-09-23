<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    //
    protected $table = 'z_tokens';

    protected $fillable = ['dt', 'users_id', 'token'];

    public $timestamps = false;


}

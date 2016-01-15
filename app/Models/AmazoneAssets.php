<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmazoneAssets extends Model
{
    protected $table = "cc_amazone_assets";

    public $timestamps = false;

    protected $fillable = ['id', 'accounts_id', 'bucket', 'region', 'secret_key', 'access_key'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZaccountsView extends Model
{
    protected $table = "z_accounts_view";

    public $timestamps = false;

    protected $fillable = [
        'id', 'fb_id', 'u_bdate', 'bdate', 'u_name', 'u_fname', 'u_lname', 'u_mname', 'u_email', 'u_gender', 'u_regdate', 'login_source', 'login_provider', 'u_avatar', 'geo_country', 'activated', 'platform',
    ];
}

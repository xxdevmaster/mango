<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zaccounts extends Model
{
    protected $table = "z_accounts";

    public $timestamps = false;

    protected $fillable = [
        'id', 'u_name', 'u_fname', 'u_lname', 'u_mname', 'u_pass', 'u_pass_forgot',
        'u_email', 'u_bdate', 'u_gender', 'u_ping', 'u_regdate', 'fb_id', 'fb_response',
        'geo_country', 'u_about', 'u_credits', 'u_person', 'last_stream_id', 'frontpage_movies', 'access_level',
        'activated', 'activation_code', 'u_avatar', 'u_avatar_src', 'login_provider', 'login_source', 'fixed_country',
        'newsletters', 'tester', 'payment_tester',
    ];
}

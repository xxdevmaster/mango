<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriptions extends Model
{
    protected $table = "cc_subscriptions";

    public $timestamps = false;

    protected $fillable = [
        'id', 'channels_id', 'title', 'regular_amount',
        'regular_frequency', 'regular_period', 'trial_amount',
        'trial_frequency', 'trial_period', 'currency', 'plan_id', 'deleted'
    ];
}

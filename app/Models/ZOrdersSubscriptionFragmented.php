<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZOrdersSubscriptionFragmented extends Model
{
    protected $table = "z_orders_subscription_fragmented";

    public $timestamps = false;

    protected $fillable = ['id', 'user_id', 'channels_id', 'orders_subscriptions_id', 'amount', 'currency', 'billing_date', 'billing_month'];
}

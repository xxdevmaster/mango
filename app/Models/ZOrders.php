<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZOrders extends Model
{
    protected $table = "z_orders";

    public $timestamps = false;

    protected $fillable = [
        'id', 'dt', 'user_id', 'films_id', 'amount', 'currency', 'status', 'expires', 'title', 'country', 'test', 'wl', 'remote_info', 'remote_token', 'rel', 'payment_method', 'payment_gateway', 'order_type', 'gift', 'gift_code'
    ];

    public function usersFilms()
    {
        return $this->hasOne('App\Film', 'id', 'films_id');
    }
}

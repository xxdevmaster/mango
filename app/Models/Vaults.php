<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vaults extends Model
{
    protected $table = "cc_vaults";

    public $timestamps = false;

    protected $fillable = ['id', 'films_id', 'companies_id', 'deleted_dt'];
	
	public function channelsVaults()
	{
		return $this->hasMany('App\Models\Channelsvaults', 'vaults_id');
	}
	
}

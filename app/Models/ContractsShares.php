<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractsShares extends Model
{
    protected $table = "cc_contracts_shares";
	
    public $timestamps = false;
	
	protected $fillable = ['id', 'contracts_id', 'companies_id', 'share_type', 'share_fee', 'share_cp', 'share_pl', 'share_ch'];

}

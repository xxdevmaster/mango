<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jobs extends Model
{
    protected $table = "cc_jobs";
	
    public $timestamps = false;
	
	protected $fillable = ['id', 'title', 'department', 'jobtitle', 'deleted'];
}

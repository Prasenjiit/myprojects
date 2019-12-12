<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DimensionMaster extends Model
{
	protected $table = 'dimension_masters';
    protected $fillable = [
        'DimensionCode', 'DimensionName', 'DimentionType', 'LeCode','UpdateDate','UpdateTime','UserCode'
    ];	
	
}

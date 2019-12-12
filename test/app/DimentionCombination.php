<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DimentionCombination extends Model
{
    protected $table = 'dimension_combination';
    protected $fillable = [
        'LedgerCode', 'CostCentre', 'Department', 'Purpose', 'LeCode', 'Active_Comb', 'UpdateDate','UpdateTime','UserCode'];
}

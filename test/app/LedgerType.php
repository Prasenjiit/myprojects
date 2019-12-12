<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LedgerType extends Model
{
    protected $table 	= 'enum_ledgertype';
	protected $fillable = ['name','status'];
}

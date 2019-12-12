<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrintSide extends Model
{
    protected $table 	= 'printside';
	protected $fillable = ['name','status'];
}

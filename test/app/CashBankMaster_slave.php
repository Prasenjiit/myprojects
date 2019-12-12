<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashBankMaster_slave extends Model
{
    protected $table = 'cash_bank_master_slave';
    protected $fillable = ['cbm_id','address_id','address_type','primary_address'];
}

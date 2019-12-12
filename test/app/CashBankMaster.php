<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashBankMaster extends Model
{
    protected $table = 'cash_bank_master';
    protected $fillable = ['Cash_BankCode','Bank_Name','IFSC','IBAN','SWIFT','LedgerAccount','RealProfitLedg','LeCode','UserCode','Cash','Warehouse'];

}

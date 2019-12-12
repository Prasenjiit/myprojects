<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class COA extends Model
{
    protected $table 	= 'coa';
	protected $fillable = ['Header_COA','LedgerAccount','LedgerName','LedgerType','OnHold','ConsolidateAccount','CostCentre','Department','Purpose','LockedInJournal','LeCode','UserCode','TR','PL','BS','Print_Side'];
}

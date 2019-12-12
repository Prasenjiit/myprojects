<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuditModel extends Model
{
     protected $table = 'tbl_audits';

    /**
    *Primary key
    */
    protected $primaryKey = 'audit_id';

    
}

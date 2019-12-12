<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuditsModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_audits';

    /**
    *Primary key
    */
    protected $primaryKey = 'audit_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
}

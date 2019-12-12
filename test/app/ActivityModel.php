<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_activities';

    /**
    *Primary key
    */
    protected $primaryKey = 'activity_id';

    
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DepartmentsModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_departments';

    /**
    *Primary key
    */
    protected $primaryKey = 'department_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'department_name', 'department_description'
    ];
}

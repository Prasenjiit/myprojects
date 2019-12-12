<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StacksModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_stacks';

    /**
    *Primary key
    */
    protected $primaryKey = 'stack_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'stack_name', 'stack_description'
    ];
   
    public function chlids()
    {
        return $this->hasMany('App\StacksColumnModel','stack_id');
    }
}

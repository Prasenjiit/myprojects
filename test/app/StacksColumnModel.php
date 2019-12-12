<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\DocumentTypesModel as DocumentTypesModel;

class StacksColumnModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_stack_columns';

    /**
    *Primary key
    */
    protected $primaryKey = 'stack_column_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'stack_column_id', 'stack_column_name', 'stack_column_type'
    ];

    public function docType()
    {
        return $this->belongsTo('App\StacksModel', 'stack_id');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentsStacksColumnModel extends Model
{
     /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_documents_stack_columns';

    /**
    *Primary key
    */
    protected $primaryKey = 'document_stack_column_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'documents_stack_column_name', 'documents_stack_column_value'
    ];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentsStacksColumnCheckoutModel extends Model
{
     /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_documents_stack_columns_checkout';

    /**
    *Primary key
    */
    protected $primaryKey = 'document_stack_column_checkout_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'documents_stack_column_name', 'documents_stack_column_value'
    ];
}


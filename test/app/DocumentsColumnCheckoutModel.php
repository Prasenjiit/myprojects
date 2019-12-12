<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentsColumnCheckoutModel extends Model
{
     /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_documents_columns_checkout';

    /**
    *Primary key
    */
    protected $primaryKey = 'document_column_checkout_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'documents_column_name', 'documents_column_value'
    ];
}


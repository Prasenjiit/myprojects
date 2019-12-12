<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentHistoryColumnModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_documents_history_columns';

    /**
    *Primary key
    */
    protected $primaryKey = 'documents_column_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'documents_column_name', 'documents_column_value'
    ];
}

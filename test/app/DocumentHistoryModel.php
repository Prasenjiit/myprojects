<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentHistoryModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_documents_history';

    /**
    *Primary key
    */
    protected $primaryKey = 'document_history_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
}

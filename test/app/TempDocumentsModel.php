<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempDocumentsModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_temp_documents';

    /**
    *Primary key
    */
    protected $primaryKey = 'document_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'document_file_name', 'document_path'
    ];
    
}

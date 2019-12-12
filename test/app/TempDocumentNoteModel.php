<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempDocumentNoteModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_temp_document_notes';

    /**
    *Primary key
    */
    protected $primaryKey = 'document_notes_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentsCheckoutModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_documents_checkout';

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
        'document_id',
    ];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\DocumentTypesModel as DocumentTypesModel;

class DocumentTypeColumnModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_document_types_columns';

    /**
    *Primary key
    */
    protected $primaryKey = 'document_type_column_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'document_type_id', 'document_type_column_name', 'document_type_column_type'
    ];

    public function docType()
    {
        return $this->belongsTo('App\DocumentTypesModel', 'document_type_id');
    }
}

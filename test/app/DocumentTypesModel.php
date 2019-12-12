<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentTypesModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_document_types';

    /**
    *Primary key
    */
    protected $primaryKey = 'document_type_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'document_type_name', 'document_type_description'
    ];
   
    public function chlids()
    {
        return $this->hasMany('App\DocumentTypeColumnModel','document_type_id');
    }

     public function childrens()
    {
      return $this->hasMany('App\DocumentTypeColumnModel', 'document_type_id','document_type_id')->orderBy('document_type_column_order','ASC');
    }
}

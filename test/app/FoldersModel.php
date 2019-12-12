<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FoldersModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_folders';

    /**
    *Primary key
    */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'text'
    ];
   
    // public function chlids()
    // {
    //     return $this->hasMany('App\DocumentTypeColumnModel','document_type_id');
    // }
}

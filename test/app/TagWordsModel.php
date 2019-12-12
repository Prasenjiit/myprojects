<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TagWordsModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_tagwords';

    /**
    *Primary key
    */
    protected $primaryKey = 'tagwords_id';

    
}

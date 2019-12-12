<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TagWordsCategoryModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_tagwords_category';

    /**
    *Primary key
    */
    protected $primaryKey = 'tagwords_category_id';

}
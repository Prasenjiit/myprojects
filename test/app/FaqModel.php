<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FaqModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_faq';

    /**
    *Primary key
    */
    protected $primaryKey = 'faq_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
}

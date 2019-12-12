<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Input;

class AppLinksModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_app_links';

    /**
    *Primary key
    */
    protected $primaryKey = 'id';

  
}

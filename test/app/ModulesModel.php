<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ModulesModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_modules';

    /**
    *Primary key
    */
    protected $primaryKey = 'module_id';

}/*<--END-->*/
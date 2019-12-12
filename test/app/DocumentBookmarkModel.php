<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class DocumentBookmarkModel extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_document_bookmarks';

    /**
    *Primary key
    */
    protected $primaryKey = 'document_bookmark_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
}

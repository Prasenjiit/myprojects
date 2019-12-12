<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class CsvDataModel extends Model
{
    protected $table = 'tbl_csv_data';
    protected $fillable = ['csv_data_filename', 'csv_data_header', 'document_type_id', 'csv_data'];
}

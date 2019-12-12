<?php
return [
    /*
    |--------------------------------------------------------------------------
    | User Defined Variables
    |--------------------------------------------------------------------------
    |
    | This is a set of variables that are made specific to this application
    | that are better placed here rather than in .env file.
    | Use config('your_key') to get the values.
    |
    */
    'import_file' => env('import_file','fileeazy_username_import_data_'),
    'master_file' => env('master_file','fileeazy_username_master_data_'),
    'export_file' => env('export_file','fileeazy_username_export_'),
    'error_file'  => env('error_file','fileeazy_username_error_'),
    'blkcheckout_file'  => env('blkcheckout_file', 'fileeazy_username_checkout_'),
    'search_export_file' => env('search_export_file','fileeazy_username_export_csv_'),
    'search_export_file_pdf' => env('search_export_file_pdf','fileeazy_username_export_pdf_'),
    'export_zip'  => env('export_zip','fileeazy_username_export_'),
];
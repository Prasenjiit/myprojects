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
    'import_file' => env('import_file','import.csv'),
    'sample_file' => env('sample_file','fileeazy_sample_data.csv'),
    'master_file' => env('master_file','fileeazy_master_data.csv'),
    'export_file' => env('export_file','fileeazy_export.csv'),
    'error_file'  => env('error_file', 'fileeazy_error_data.csv'),
];
<?php
namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Http\Helpers\EnvironmentManager;
use App\Http\Helpers\FinalInstallManager;
use App\Http\Helpers\InstalledFileManager;
use App\Http\Events\LaravelInstallerFinished;

use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use App\Http\Events\EnvironmentSaved;
use Validator;
use Illuminate\Validation\Rule;
use App\database\seeds\DatabaseSeeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PDO;   
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Output\BufferedOutput;

class ImportTableController extends Controller
{
/**
     * @var EnvironmentManager
     */
    protected $EnvironmentManager;

    /**
     * @param EnvironmentManager $environmentManager
     */
    public function __construct(EnvironmentManager $environmentManager)
    {
        $this->EnvironmentManager = $environmentManager;
    }

    /**
     * Display the requirements page.
     *
     * @return \Illuminate\View\View
     */
    public function importTable(Redirector $redirect)
    {
        $results = $this->EnvironmentManager->migrateandSeed();
       
        // do {
        //     //$users = DB::table('tree_struct')->get();
        //     //print_r($users);
        //     // $pdo = new PDO("mysql:host=".$hostName, $userName, $passwd);
        //     // $output = $pdo->exec(
        //     //     'CREATE DATABASE IF NOT EXISTS %s ;', $request->database_name
        //     // )); 

        //     //$this->EnvironmentManager->migrateandSeed($request);
        //     echo $tables  = Schema::hasTable('tree_struct');

        // }while($tables==1);   

        // exit();

        return $redirect->route('final')->with(['results' => $results]);
    }
}
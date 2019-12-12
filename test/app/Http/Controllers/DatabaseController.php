<?php
namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Http\Helpers\DatabaseManager;

class DatabaseController extends Controller
{
    /**
     * @var DatabaseManager
     */
    private $databaseManager;

    /**
     * @param DatabaseManager $databaseManager
     */
    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }

    /**
     * Migrate and seed the database.
     *
     * @return \Illuminate\View\View
     */
    public function database()
    {
        $response = $this->databaseManager->migrateAndSeed();
        //return redirect()->route('final')->with(['message' => $response]);
        //return redirect('final');
        //return redirect('')->route('final');
    }
}

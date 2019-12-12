<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use App\Http\Helpers\EnvironmentManager;
use App\Http\Events\EnvironmentSaved;
use Validator;
use Illuminate\Validation\Rule;
use App\database\seeds\DatabaseSeeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PDO;   
use mysqli;
use Session;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Output\BufferedOutput;

class EnvironmentController extends Controller
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
     * Display the Environment menu page.
     *
     * @return \Illuminate\View\View
     */
    public function environmentMenu()
    {
        return view('installer.environment');
    }

    /**
     * Display the Environment page.
     *
     * @return \Illuminate\View\View
     */
    public function environmentWizard()
    {
        $envConfig = $this->EnvironmentManager->getEnvContent();
        return view('installer.environment-wizard', compact('envConfig'));
    }

    /**
     * Display the Environment page.
     *
     * @return \Illuminate\View\View
     */
    public function environmentClassic()
    {
        $envConfig = $this->EnvironmentManager->getEnvContent();
        return view('installer.environment-classic', compact('envConfig'));
    }

    /**
     * Processes the newly saved environment configuration (Classic).
     *
     * @param Request $input
     * @param Redirector $redirect
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveClassic(Request $input, Redirector $redirect)
    {       
        $result = $this->EnvironmentManager->createDbClassic($input);
        $message = $this->EnvironmentManager->saveFileClassic($input);
        event(new EnvironmentSaved($input));
        //return $redirect->route('environmentClassic')->with(['message' => $message]);
    }

    

    /**
     * Processes the newly saved environment configuration (Form Wizard).
     *
     * @param Request $request
     * @param Redirector $redirect
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveWizard(Request $request, Redirector $redirect)
    {

        // $rules = config('installer.environment.form.rules');
        $message = [
            'environment_custom.required_if' => trans('installer_messages.environment.wizard.form.name_required'),
        ];
        // $validator = Validator::make($request->all(), $rules, $messages);
        // if ($validator->fails()) {
        //     $errors = $validator->errors();
        //     return view('installer.environment-wizard', compact('errors', 'envConfig'));
        // }
        //temp update env for seeding database            
        $results = $this->EnvironmentManager->saveFileWizard($request,'notinstalled');

        $res = $this->EnvironmentManager->CheckDB($request);
        if($res=="success"){
            $reslt = $this->EnvironmentManager->createDB($request);
            event(new EnvironmentSaved($request));
            return redirect('importTable');
        }else{
            Session::put('errmsg', '* Failed! Make sure the databae server username & password is correct');
            return redirect('environmentWizard');
        }
        // //create database, migrate table, seed table
        // 
        // //print_r($res);
        // if($res!=2){
        //     //create database, migrate table, seed table
        //     //$res = $this->EnvironmentManager->createDB($request);
        //     event(new EnvironmentSaved($request));
        // $res =  redirect('CheckDB');
        // print_r($res);
        // }else{
        //     return redirect('environmentWizard');
        // }

               

        //return $redirect->route('importTable');
    }
}

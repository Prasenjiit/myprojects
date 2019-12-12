<?php
namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Http\Helpers\EnvironmentManager;
use App\Http\Helpers\FinalInstallManager;
use App\Http\Helpers\InstalledFileManager;
use App\Http\Events\LaravelInstallerFinished;
use DB;

class FinalController extends Controller
{

    /**
     * Update installed file and display finished view.
     *
     * @param InstalledFileManager $fileManager
     * @return \Illuminate\View\View
     */
    public function finish(InstalledFileManager $fileManager, FinalInstallManager $finalInstall, EnvironmentManager $environment)
    {
        $finalMessages = $finalInstall->runFinal();
        $finalStatusMessage = $fileManager->update();

        //update env notinstalled to installed 
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, str_replace('APP_INSTALL=notinstalled','APP_INSTALL=installed',file_get_contents($path)
            ));
        }
        $finalEnvFile = $environment->getEnvContent();

        event(new LaravelInstallerFinished);
        return view('installer.finished', compact('finalMessages', 'finalStatusMessage', 'finalEnvFile'));
    }
}

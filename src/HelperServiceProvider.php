<?php namespace neverbehave\helpers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * register the service provider
     *
     * @return void
     */
    public function register()
    {
        //register commands
        $this->commands([
            Commands\HelperMakeCommand::class,
        ]);

        //include the active package helpers
        foreach (config('helpers.package_helpers', []) as $activeHelper) {

            $file = __DIR__ . '/Helpers/' . $activeHelper . '.php';

            if (file_exists($file)) {
                require_once($file);
            }
        }

        //load custom helpers with a mapper
        if (count(config('helpers.custom_helpers', []))) {

            foreach (config('helpers.custom_helpers', []) as $customHelper) {

                $file = app()->path() . '/' . $this->getHelpersDirectory() . '/' . $customHelper . '.php';

                if(file_exists($file)) {
                    require_once($file);
                }
            }
        }

        //load custom helpers automatically
        else {

            //include the custom helpers
            foreach (glob(app()->path() . '/' . $this->getHelpersDirectory() . '/*.php') as $file) {
                require_once($file);
            }
        }
    }

    /**
     * boot the service provider
     *
     * @return void
     */
    public function boot()
    {
        //publish configuration
        $this->publishes([
            __DIR__ . '/config/helpers.php' => $this->config_path('helpers.php'),
        ], 'config');
    }

    /**
     * get the directory the helpers are stored in
     *
     * @return mixed
     */
    public function getHelpersDirectory()
    {
        return config('helpers.directory', 'Helpers');
    }
    
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

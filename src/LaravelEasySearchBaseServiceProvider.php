<?php


namespace AppsLab\LaravelEasySearch;


use AppsLab\LaravelEasySearch\Console\FilterCommand;
use Illuminate\Support\ServiceProvider;

class LaravelEasySearchBaseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()){
            $this->registerPublishing();
        }
        $this->registerResources();
    }

    public function register()
    {
        $this->commands([
            FilterCommand::class
        ]);
    }

    private function registerResources()
    {
        $file = __DIR__.'/../src/Support/helper.php';
        if (file_exists($file)){
            require_once($file);
        }
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');
        $this->registerFacades();
    }

    protected function registerPublishing()
    {
        $this->publishes([
            __DIR__.'/../config/easy-search.php' => 'config/easy-search.php'
        ],'easy-search');
    }

    protected function registerFacades()
    {
        $this->app->singleton('Search', function ($app){
            return new \AppsLab\LaravelEasySearch\Search();
        });
    }
}
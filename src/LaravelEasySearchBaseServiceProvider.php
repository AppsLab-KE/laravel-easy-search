<?php


namespace AppsLab\LaravelEasySearch;


use AppsLab\LaravelEasySearch\Builds\DateBuild;
use AppsLab\LaravelEasySearch\Builds\GeneralBuild;
use AppsLab\LaravelEasySearch\Builds\Integer;
use AppsLab\LaravelEasySearch\Builds\IntegerBuild;
use AppsLab\LaravelEasySearch\Console\FilterCommand;
use Illuminate\Support\ServiceProvider;

class LaravelEasySearchBaseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
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
        if (file_exists($file)) {
            require_once($file);
        }
        // $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');
        $this->registerFacades();
        $this->registerBuilds();
    }

    protected function registerPublishing()
    {
        $this->publishes([
            __DIR__.'/../config/easy-search.php' => 'config/easy-search.php'
        ],'easy-search-config');

        $this->publishes([
            __DIR__.'/../resources/stubs/BuildServiceProvider.stub' => app_path('Providers/BuildServiceProvider.php')
        ],'easy-search-provider');
    }

    protected function registerFacades()
    {
        $this->app->singleton('Search', function ($app) {
            return new \AppsLab\LaravelEasySearch\Search();
        });
    }

    protected function registerBuilds()
    {
        \AppsLab\LaravelEasySearch\Facades\Search::builds([
            GeneralBuild::class,
            IntegerBuild::class,
            DateBuild::class
        ]);
    }
}
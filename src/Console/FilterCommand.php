<?php


namespace AppsLab\LaravelEasySearch\Console;


use AppsLab\LaravelEasySearch\Contracts\GeneratorCommand;
use AppsLab\LaravelEasySearch\Facades\Search;
use AppsLab\LaravelEasySearch\Repositories\DatabaseRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class FilterCommand extends GeneratorCommand
{
    /**
     * Add filter for searching model
     * @var string
     */
    protected $signature = 'make:filter {columns* : columns name or all} {--c|condition=} {--e|exclude= : Columns which should be excluded}';

    protected $description = "This command is used to create search filters";

    public function handle()
    {
        $name = $this->qualifyClass("Demoss");
        $path =  $this->getPath($name);
        $this->makeDirectory($path);
        $this->files->put($path, $this->buildClass($name));

        dd($this->laravel['path'], $this->rootNamespace(), $this->makeDirectory($path));

        if (Search::configNotPublished()){
            return $this->warn('Publish laravel easy search config file');
        }
        $getUserTableName = $this->ask("Table: Enter the table you want to create filter for");
        $getUserTableName = ctype_upper($getUserTableName) ? strtolower($getUserTableName) : $getUserTableName;

        $tableName = Str::snake(Str::camel($getUserTableName));

        if (DatabaseRepository::conn($tableName)->tableExists()){
            $getEnteredColumns = $this->argument('columns');
            $getDefaultCondition = $this->option("condition");
            $getDefaultCondition = trim($getDefaultCondition == "==" ? "=" : str_replace("=","", $getDefaultCondition));

            $dbRepo = DatabaseRepository::conn($tableName);

            if (in_array("all", $getEnteredColumns)){
                $getEnteredColumns = $dbRepo->getTableColumns();
            }

            $exclude = $this->option('exclude');
            $exclude = $exclude ? explode(",",trim(str_replace("=","",$exclude))) : [];

            $getValidTableColumns = $dbRepo->validateTableColumns($dbRepo->excludeColumns($getEnteredColumns, $exclude));

            if (count($getValidTableColumns) > 1){
                $builderOption = $this->choice("Add query to filter one-by-one or autogenerate?",["OneByOne","Autogenerate"],"Autogenerate");

                if ($builderOption == "OneByOne") {
                    $this->getFilters($getValidTableColumns);
                }

                if ($builderOption == "Autogenerate") {
                    $this->getFilters($getValidTableColumns);
                }
            }

            $queryType = $this->anticipate("Enter <fg=white>". implode($getValidTableColumns) ."</> query type without/with query condition eg <fg=white>where</> or <fg=white>whereDate|!=</>. Press Enter to accept default",["where|="],"where|Like");

        }
    }

    protected function qualifyClass($name)
    {
        return $name;
//        $name = ltrim($name, '\\/');
//        $rootNamespace =  config('easy-search.namespace.filter');
//        if (Str::startsWith($name, $rootNamespace)){
//            return $name;
//        }
//        $name = str_replace("/","\\", $name);
//        return dd($this->qualifyClass($this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name));
    }

    protected function getStub()
    {
        return __DIR__ . '/../../resources/stubs/DummyFilter.stub';
    }

    public function getFilters($tableColumns, $autogenerate = false): array
    {
        foreach ($tableColumns as $tableColumn){
            $this->anticipate("Enter <fg=white>{$tableColumn}</> query type without/with query condition eg 
            <fg=white>where</> or <fg=white>whereDate|!=</>. Press Enter to accept default",["where|="],"where|Like");
        }
    }

    protected function getNamespace($name)
    {
        return filter_namespace();
    }

    protected function getPath($name)
    {
        return filter_path($name.".php");
    }
}
<?php

namespace AppsLab\LaravelEasySearch\Console;

use AppsLab\LaravelEasySearch\Contracts\GeneratorCommand;
use AppsLab\LaravelEasySearch\Exceptions\QueryBuildError;
use AppsLab\LaravelEasySearch\Facades\Search;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class FilterCommand extends GeneratorCommand
{
    /**
     * Add filter for searching model.
     * @var string
     */
    protected $signature = 'make:filter {columns* : columns name or all} {--t|table=} {--e|exclude= : Columns which should be excluded} {--force}';
    protected $table;

    protected $description = 'This command is used to create search filters';

    public function handle()
    {
        if (Search::configNotPublished()) {
            Artisan::call('vendor:publish', [
                '--provider' => "AppsLab\LaravelEasySearch\LaravelEasySearchBaseServiceProvider",
            ]);

            $this->info($this->files->get(__DIR__.'/../../resources/stubs/hello.stub'));
        }

        $getTableName = $this->option('table');

        $getTableName = $getTableName ? str_replace('=', '', $getTableName) : null;

        if (! $getTableName) {
            $getTableName = $this->ask('Enter table name:');
        }

        $getUserTableName = ctype_upper($getTableName) ? strtolower($getTableName) : $getTableName;

        $this->table = Str::snake(Str::camel($getUserTableName));
        $dbRepo = Search::table($this->table);

        if ($dbRepo->tableExists()) {
            $getEnteredColumns = $this->argument('columns');

            if (in_array('all', $getEnteredColumns)) {
                $getEnteredColumns = $dbRepo->getTableColumns();
            }

            $exclude = $this->option('exclude');
            $exclude = $exclude ? explode(',', trim(str_replace('=', '', $exclude))) : [];

            $getValidTableColumns = $dbRepo->validateTableColumns($dbRepo->excludeColumns($getEnteredColumns, $exclude));
            $autogenerate = true;

            if (! $this->confirm('Autogenerate query?')) {
                $autogenerate = false;
            }

            if (count($getValidTableColumns) < 1) {
                $this->error('Column not found in the table. Columns are case sensitive');

                return;
            }

            $columnsWithQuery = $this->getFilters($getValidTableColumns, $autogenerate);

            $getTableBar = $this->output->createProgressBar(count($columnsWithQuery));
            $getTableBar->start();

            $filtersGenerated = [];

            foreach ($columnsWithQuery as $key => $columnData) {
                $folder = $this->table;
                $fileName = Str::studly($key);
                $name = $this->qualifyClass($folder.'/'.$fileName);
                $force = $this->option('force');

                if (! $force && file_exists(filter_path($name.'.php'))) {
                    continue;
                }

                $data = [
                    'name' => $fileName,
                    'column' => $key,
                    'folder' => $folder,
                    'condition' => $columnData['condition'],
                    'query' => $columnData['query'],
                ];

                $path = $this->getPath($name);

                $this->makeDirectory($path);
                $this->files->put($path, $this->buildClass($data));

                array_push($filtersGenerated, [
                    $name, $columnData['query'], ($columnData['condition'] == '' ? '=' : $columnData['condition']),
                ]);

                $getTableBar->advance();
            }
            $getTableBar->finish();
            $this->line("\n");
            $this->table(['Filter', 'QueryType', 'Condition'], $filtersGenerated);
            $this->line('You can change your query on specific Filter in dir '. config('easy-search.location.filter'));
            $this->info("\nAll good, filters build successfully \nGo search 🔎");

            return;
        }

        $this->error("The table {$this->table} does not exist in the app database");
    }

    protected function qualifyClass($name)
    {
        return $name;
    }

    protected function getStub()
    {
        return __DIR__ . '/../../resources/stubs/DummyFilter.stub';
    }

    public function getFilters($tableColumns, $autogenerate = false): array
    {
        $columnsWithQuery = null;

        foreach ($tableColumns as $tableColumn) {
            $tableColumn = strtolower($tableColumn);
            $autogenerateQuery = Search::autogenerateQuery($this->table, $tableColumn);

            if (! array_key_exists('condition', $autogenerateQuery) && ! array_key_exists('query', $autogenerateQuery)) {
                throw new QueryBuildError('Query builder response does not have condition or query arguments');
            }
            $defaultQuery = $autogenerateQuery['query'] . ($autogenerateQuery['condition'] != '' ? '|'.$autogenerateQuery['condition'] : '');

            $userQuery = $autogenerate ? $defaultQuery : $this->anticipate("Enter <fg=white>{$tableColumn}</> query and condition(optional) separated by | eg <fg=white>orWhere</> or <fg=white>whereDate|!=</>", config('easy-search.queries'), $defaultQuery);

            $formartQuery = explode('|', $userQuery);

            if (array_key_exists(0, $formartQuery)) {
                $query['query'] = trim($formartQuery[0]) == '' ? 'orWhere' : trim($formartQuery[0]);
            }

            $query['condition'] = array_key_exists(1, $formartQuery) ? trim($formartQuery[1]) : '';

            $columnsWithQuery[$tableColumn] = $query;
        }

        return $columnsWithQuery;
    }

    /**
     * Return namespace
     * @param string $name
     * @return \Illuminate\Config\Repository|mixed|string
     */
    protected function getNamespace($name)
    {
        return filter_namespace();
    }

    /**
     * Get file path
     * @param string $name
     * @return \Illuminate\Config\Repository|mixed|string
     */
    protected function getPath($name)
    {
        return filter_path($name.'.php');
    }
}

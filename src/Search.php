<?php

namespace AppsLab\LaravelEasySearch;

use AppsLab\LaravelEasySearch\Repositories\BuildRepository;
use AppsLab\LaravelEasySearch\Repositories\DatabaseRepository;
use AppsLab\LaravelEasySearch\Repositories\ModelRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Search
{
    protected $builds = [];

    public function configNotPublished()
    {
        return is_null(config('easy-search'));
    }

    /**
     * Model to be searched.
     *
     * @param $model
     * @return ModelRepository
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function model($model): ModelRepository
    {
        return new ModelRepository(app()->make($model));
    }

    /**
     * @param array $buildsg
     */
    public function builds(array $builds)
    {
        $this->builds = array_merge($this->builds, $builds);
    }

    public function availableBuilds()
    {
        return array_reverse($this->builds);
    }

    public function build()
    {
        return BuildRepository::build();
    }

    public function table($tableName)
    {
        return DatabaseRepository::conn($tableName);
    }

    /**
     * Generate query parameters.
     * @param $tableName
     * @param string $columnName
     * @return array
     */
    public function autogenerateQuery($tableName, string $columnName): array
    {
        try {
            $getColumnType = $this->table($tableName)->getColumnType($columnName);
        } catch (\Exception $exception) {
            $getColumnType = 'string';
        }

        $className = $this->build()->classNameFromColumnType($getColumnType);

        return $this->build()->query($className);
    }

    /**
     * Get model filter.
     * @param string $filterName
     * @param null $namespace
     * @return string|null
     * @throws ClassDoesNotExist
     */
    public function getFilter(string $filterName, $namespace = null)
    {
        $filterName = Str::studly($filterName);
        $filterClass = filter_class($filterName, $namespace);

        if (! class_exists($filterClass)) {
            if (env('APP_ENV') != 'local') {
                return;
            }
            throw new ClassDoesNotExist("Filter class {$filterClass}.php does not exist, run php artisan make:filter columnname");
        }

        return $filterClass;
    }
}

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

    public function model($model)
    {
        return new ModelRepository(app()->make($model));
    }

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

    public function autogenerateQuery($tableName, string $columnName): array
    {
        $getColumnType = $this->table($tableName)->getColumnType($columnName);
        $className = $this->build()->classNameFromColumnType($getColumnType);

        return $this->build()->query($className);
    }

    public function getFilter(string $filterName, $namespace = null)
    {
        $filterName = Str::studly($filterName);
        //TODO: class does not exist check
        $filterNamespace = filter_class($filterName, $namespace);

        if (! $this->isValidFilter($filterName)) {
            throw new ClassDoesNotExist('Class does not exist');
        }

        return $filterNamespace;
    }

    public function isValidFilter($filterClassName) : bool
    {
        return file_exists(filter_path($filterClassName.'.php'));
    }
}

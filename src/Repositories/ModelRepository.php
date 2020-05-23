<?php

namespace AppsLab\LaravelEasySearch\Repositories;

use AppsLab\LaravelEasySearch\Facades\Search;
use Illuminate\Database\Eloquent\Model;

class ModelRepository
{
    public $model;
    public $modelQuery;
    protected $allowedColumns = null;
    protected $sortBy = null;
    public $request;
    public $allowPaginate = false;
    public $ignoredFilters = ['page'];
    protected $perPage = 10;
    public $allColumnsSearchKey = 'search';
    public $allowAllColumnsSearch = false;
    public $allColumnsFilters = [];
    protected $relations = [];
    protected $addedSearch = [];

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->modelQuery = $model->newQuery();
    }

    /**
     * Apply filter to model.
     *
     * @param array $replaceFilters
     * @return void
     */
    public function applyFilters(array $replaceFilters = []): ModelRepository
    {
        $this->request = \request();
        $getQueryFilters = array_merge($this->formatFilters($replaceFilters, $this->request->all()), $this->addedSearch);

        return $this->results($getQueryFilters);
    }

    private function results(array $getQueryFilters): ModelRepository
    {
        if ($this->allowAllColumnsSearch && array_key_exists($this->allColumnsSearchKey, $getQueryFilters)) {
            foreach ($this->allColumnsFilters as $key) {
                $getQueryFilters[$key] = $getQueryFilters[$this->allColumnsSearchKey];
            }

            unset($getQueryFilters[$this->allColumnsSearchKey]);
        }

        $getQueryFilters = $this->removeIgnoredFilters($getQueryFilters);

        $this->modelQuery = count($getQueryFilters) > 0 ? $this->model->apply($getQueryFilters) : $this->model->newQuery();

        return $this;
    }

    private function removeIgnoredFilters($getQueryFilters): array
    {
        foreach ($getQueryFilters as $key => $getQueryFilter) {
            if (in_array($key, $this->ignoredFilters())) {
                unset($getQueryFilters[$key]);
            }
        }

        return $getQueryFilters;
    }

    private function formatFilters($replaceFilters, $getQueryFilters): array
    {
        foreach ($replaceFilters as $key => $replacement) {
            if (is_array($replacement)) {
                foreach ($replacement as $innerKey => $innerReplaceFilters) {
                    $getQueryFilters = $this->replaceKeys($innerKey, $innerReplaceFilters, $getQueryFilters);
                }
            } else {
                $getQueryFilters = $this->replaceKeys($key, $replacement, $getQueryFilters);
            }
        }

        return $getQueryFilters;
    }

    /**
     * Replace table search parameters with filters.
     *
     * @param [type] $key
     * @param [type] $replacement
     * @param [type] $getQueryFilters
     * @return array
     */
    private function replaceKeys($key, $replacement, array $getQueryFilters): array
    {
        if (array_key_exists($key, $getQueryFilters)) {
            $getQueryFilters[$replacement] = $getQueryFilters[$key];
            unset($getQueryFilters[$key]);
        }

        return $getQueryFilters;
    }

    /**
     * @param string|null $parameter
     * @param array $ignoredColumns
     * @return $this
     */
    public function searchAllColumns(string $parameter = null, array $ignoredColumns = [])
    {
        $this->allowAllColumnsSearch = true;
        $this->allColumnsSearchKey = $parameter ?? $this->allColumnsSearchKey;

        $this->allColumnsFilters = array_filter(DatabaseRepository::conn($this->model->getTable())->getTableColumns(),
            function ($column) use ($ignoredColumns) {
                return ! in_array($column, $ignoredColumns);
            });

        $this->allowedColumns = DatabaseRepository::conn($this->model->getTable())->getTableColumns();

        return $this;
    }

    /**
     * Get sortBy parameters and map them to @sortBy.
     *
     * @param [type] $field
     * @param string $type
     * @return void
     */
    public function sortBy($field, $type = 'ASC'): ModelRepository
    {
        $this->sortBy = [$field, $type];

        return $this;
    }

    private function applySortBy($query)
    {
        return $this->sortBy ? $query->orderBy(...$this->sortBy) : $query;
    }

    /**
     * The columns allowed to be returned from the table.
     *
     * @param array $columns
     * @return void
     */
    public function allowedColumns(array $columns): self
    {
        $this->allowedColumns = $columns;

        return $this;
    }

    /**
     * Get method from model.
     *
     * @return void
     */
    public function get()
    {
        return $this->applyAllowedColumns($this->modelQuery);
    }

    public function addRelation($relation)
    {
        if (is_string($relation)) {
            array_push($this->relations, $relation);
        }

        if (is_array($relation)) {
            $this->relations = array_merge($this->relations, $relation);
        }

        return $this;
    }

    public function first()
    {
        return $this->applyAllowedColumns($this->modelQuery)->first();
    }

    /**
     * Filter the allowed columns from he search.
     *
     * @param $query
     * @return mixed
     */
    private function applyAllowedColumns($query)
    {
        $query = $this->applySortBy($query)->with($this->relations);

        if ($this->allowPaginate) {
            return $this->allowedColumns ? $query->paginate($this->perPage, $this->allowedColumns) : $query->paginate($this->perPage);
        }

        return $this->allowedColumns ? $query->get($this->allowedColumns) : $query->get();
    }

    public function addSearch(array $filters): ModelRepository
    {
        $this->addedSearch = $filters;

        return $this;
    }

    /**
     * Paginate method from model.
     *
     * @param [type] $perPage
     * @return void
     */
    public function paginate($perPage)
    {
        $this->perPage = $perPage;
        $this->allowPaginate = true;

        return $this->applyAllowedColumns($this->modelQuery);
    }

    public function buildQuery($queryType = null, $queryParameters = null)
    {
        if ($queryType === null|| $queryParameters === null){
            return $this;
        }

        $this->modelQuery = $this->modelQuery->{$queryType}(...$queryParameters);

        return $this;
    }

    private function ignoredFilters()
    {
        return $this->ignoredFilters;
    }
}

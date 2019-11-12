<?php


namespace AppsLab\LaravelEasySearch\Repositories;

use AppsLab\LaravelEasySearch\Facades\Search;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ModelRepository
{
    public $model;
    public $modelQuery;
    protected $allowedColumns = null;
    protected $sortBy = null;
    protected $universalColumns = [];
    public $request;
    public $allowPaginate = false;
    public $ignoredFilters = ['page'];
    protected $perPage = 10;
    public $universalSearchKey = 'search';
    public $allowUniversalSearch = false;
    //    public $search

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Apply filter to model
     *
     * @param array $replaceFilters
     * @return void
     */
    public function applyFilters(array $replaceFilters = [])
    {
        $this->request = \request();
        $getQueryFilters = $this->request->all();

        foreach ($replaceFilters as $key => $replacement) {
            if (is_array($replacement)) {
                foreach ($replacement as $innerKey => $innerReplaceFilters) {
                    $getQueryFilters = $this->replaceKeys($innerKey, $innerReplaceFilters, $getQueryFilters);
                }
            } else {
                $getQueryFilters = $this->replaceKeys($key, $replacement, $getQueryFilters);
            }
        }

        if ($this->allowUniversalSearch && array_key_exists($this->universalSearchKey, $getQueryFilters)) {
            foreach ($this->universalColumns as $key) {
                $getQueryFilters[$key] = $getQueryFilters[$this->universalSearchKey];
            }

            unset($getQueryFilters[$this->universalSearchKey]);
        }

        foreach ($getQueryFilters as $key => $getQueryFilter) {
            if (in_array($key, $this->ignoredFilters())) {
                unset($getQueryFilters[$key]);
            }
        }

        $this->modelQuery = count($getQueryFilters) > 0 ? $this->model->apply($getQueryFilters) : $this->model->newQuery();

        return $this;
    }

    /**
     * Replace table search parameters with filters
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

    public function applyUniversalSearch(string $parameter = null)
    {
        $this->allowUniversalSearch = true;
        $this->universalSearchKey = $parameter ? $parameter : $this->universalSearchKey;

        $this->universalColumns = DatabaseRepository::conn($this->model->getTable())->getTableColumns();
        $allowedColumns = $this->allowedColumns ? $this->allowedColumns : [];

        $this->universalColumns = array_filter($allowedColumns, function ($column) use ($allowedColumns) {
            return in_array($column, $allowedColumns);
        });

        return $this;
    }

    /**
     * Get sortBy parameters and map them to @sortBy
     *
     * @param [type] $field
     * @param string $type
     * @return void
     */
    public function sortBy($field, $type = 'ASC')
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
    public function allowedColumns(array $columns)
    {
        $this->allowedColumns = $columns;

        if ($this->allowUniversalSearch) {
            $this->applyUniversalSearch();
        }

        return $this;
    }

    /**
     * Get method from model
     *
     * @return void
     */
    public function get()
    {
        return $this->allowedColumns($this->modelQuery);
    }

    private function applyAllowedColumns($query)
    {
        $query = $this->applySortBy($query);

        if ($this->allowPaginate) {
            return $this->allowedColumns ? $query->paginate($this->perPage, $this->allowedColumns) : $query->paginate($this->perPage);
        }

        return $this->allowedColumns ? $query->get($this->allowedColumns) : $query->get();
    }

    /**
     * Paginate method from model
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

    public function addQuery($queryType, $queryParameters)
    {
        $this->modelQuery = $this->modelQuery->{$queryType}(...$queryParameters);
        return $this;
    }

    private function ignoredFilters()
    {
        return $this->ignoredFilters;
    }
}

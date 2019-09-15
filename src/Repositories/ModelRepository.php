<?php


namespace AppsLab\LaravelEasySearch\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ModelRepository
{
    public $model;
    public $modelQuery;
    protected $allowedColumns = null;
    protected $sortBy;
    public $request;

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
    public function applyFilters(array $replaceFilters = null)
    {
        $this->request = \request();
        $getQueryFilters = $this->request->all();



        if ($replaceFilters) {
            foreach ($replaceFilters as $key => $replacement) {
                if (is_array($replacement)) {
                    foreach ($replacement as $innerKey => $innerReplaceFilters) {
                        $getQueryFilters = $this->replaceKeys($innerKey, $innerReplaceFilters, $getQueryFilters);
                    }
                } else {
                    $getQueryFilters = $this->replaceKeys($key, $replacement, $getQueryFilters);
                }
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

    /**
     * The columns allowed to be returned from the table.
     *
     * @param array $columns
     * @return void
     */
    public function allowedColumns(array $columns)
    {
        $this->allowedColumns = $columns;

        return $this;
    }

    /**
     * Get method from model
     *
     * @return void
     */
    public function get()
    {
        if ($this->allowedColumns) {
            return $this->modelQuery->get($this->allowedColumns);
        }

        return $this->modelQuery->get();
    }

    /**
     * Paginate method from model
     *
     * @param [type] $perPage
     * @return void
     */
    public function paginate($perPage)
    {
        if ($this->allowedColumns) {
            return $this->modelQuery->paginate($perPage, $this->allowedColumns);
        }

        return $this->modelQuery->paginate($perPage);
    }
}

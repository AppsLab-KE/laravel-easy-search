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

    public function applyFilters(array $replaceFilters = null)
    {
        $this->request = \request();
        $getQueryFilters = $this->request->all();

        if ($replaceFilters){
            foreach ($replaceFilters as $key => $replacement){
                if (is_array($replacement)){
                    foreach ($replacement as $innerKkey => $innerReplaceFilters){
                        $getQueryFilters = $this->replaceKeys($innerKkey, $innerReplaceFilters, $getQueryFilters);
                    }
                }
                else{
                    $getQueryFilters = $this->replaceKeys($key, $replacement, $getQueryFilters);
                }
            }
        }

        $this->modelQuery = $this->model->apply($getQueryFilters);

        return $this;
    }

    private function replaceKeys($key, $replacement, $getQueryFilters): array
    {
        if (array_key_exists($key, $getQueryFilters)){
            $getQueryFilters[$replacement] = $getQueryFilters[$key];
            unset($getQueryFilters[$key]);
        }

        return $getQueryFilters;
    }

    public function sortBy($field, $type = 'DESC')
    {
        $this->sortBy = [$field, $type];

        return $this;
    }

    public function allowColumns(array $columns)
    {
        $this->allowedColumns = $columns;

        return $this;
    }

    public function get()
    {
        if ($this->allowedColumns){
            return $this->modelQuery->get($this->allowedColumns);
        }
        return $this->modelQuery->get();
    }

    public function paginate($perPage)
    {
        if ($this->allowedColumns){
            return $this->modelQuery->paginate($perPage, $this->allowedColumns);
        }

        return $this->modelQuery->paginate($perPage);
    }
}
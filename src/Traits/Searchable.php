<?php


namespace AppsLab\LaravelEasySearch\Traits;


use AppsLab\LaravelEasySearch\Facades\Search;
use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    public function apply(array $searchItems)
    {
        $query = $query ?? $this->newQuery();

        foreach ($searchItems as $filterName => $searchItem) {
            $filter = Search::getFilter($this->getTable() . "_" . $filterName);
            $class = new  \ReflectionClass($filter);
            $class = $class->getName();
            $query = $class::apply($query, $searchItem);
        }

        return $query;
    }
}

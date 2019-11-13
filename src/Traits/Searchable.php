<?php

namespace AppsLab\LaravelEasySearch\Traits;

use AppsLab\LaravelEasySearch\Facades\Search;

trait Searchable
{
    public function apply(array $searchItems)
    {
        $query = $query ?? $this->newQuery();

        foreach ($searchItems as $filterName => $searchItem) {
            $filter = Search::getFilter($filterName, filter_namespace().'\\'.$this->getTable());

            if (! $filter){
                continue;
            }

            $class = new  \ReflectionClass($filter);
            $class = $class->getName();
            $query = $class::apply($query, $searchItem);
        }

        return $query;
    }
}

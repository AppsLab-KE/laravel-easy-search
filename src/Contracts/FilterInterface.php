<?php

namespace AppsLab\LaravelEasySearch\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface FilterInterface
{
    public static function apply(Builder $query, $value);
}

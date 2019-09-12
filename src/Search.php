<?php


namespace AppsLab\LaravelEasySearch;


class Search
{
    public function configNotPublished()
    {
        return is_null(config('laravel-easy-search'));
    }
}
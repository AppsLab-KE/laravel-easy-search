<?php


namespace AppsLab\LaravelEasySearch\Facades;


use Illuminate\Support\Facades\Facade;

class Search extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'Search';
    }
}
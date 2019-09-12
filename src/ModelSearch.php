<?php


namespace AppsLab\LaravelEasySearch;


use Illuminate\Http\Request;

class ModelSearch
{
    public static function apply(Request $request, string $modelName)
    {
        return $modelName;
    }
}
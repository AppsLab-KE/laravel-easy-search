<?php

if (! function_exists('filter_path')) {
    function filter_path($fileName, $filterLocation = null)
    {
        $filterPath = config('easy-search.location.filter');

        $filePath = $fileName ? '/'. ltrim($fileName, '/') : '';

        if (is_null($filterLocation)) {
            if (empty($fileName)) {
                return $filterPath;
            }
        }

        return $filterPath.$filePath;
    }
}

function filter_class($filterName, $namespace = null)
{
    $filterPath = config('easy-search.location.filter');

    if ($namespace) {
        $filterName = str_replace(($namespace.'\\'), '', $filterName);
    }

    if (! (new \Illuminate\Filesystem\Filesystem())->isDirectory($filterPath)) {
        throw new \AppsLab\LaravelEasySearch\ClassDoesNotExist('Filter dir does not exist');
    }

    $namespace = $namespace ?? config('easy-search.namespace.filter');

    return "$namespace\\$filterName";
}

function is_filter_dir_available()
{
    $filterPath = config('easy-search.location.filter');

    if (! (new \Illuminate\Filesystem\Filesystem())->isDirectory($filterPath)) {
        throw  new \AppsLab\LaravelEasySearch\Exceptions\FilterDirectoryNotAvailable("{$filterPath} not available make sure you have it created");
    }

    return $filterPath;
}
function filter_namespace()
{
    $filterPath = is_filter_dir_available();
    $namespace = config('easy-search.namespace.filter');

    return $namespace;
}

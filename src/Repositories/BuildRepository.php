<?php

namespace AppsLab\LaravelEasySearch\Repositories;

use AppsLab\LaravelEasySearch\Builds\GeneralBuild;
use AppsLab\LaravelEasySearch\ClassDoesNotExist;
use AppsLab\LaravelEasySearch\Facades\Search;
use Illuminate\Support\Str;
use ReflectionClass;

class BuildRepository
{
    public static function build()
    {
        return new BuildRepository();
    }

    public function query($buildClassName = 'GeneralBuild', $condition = null, $queryType = null)
    {
        $class = $this->getBuild($buildClassName);

        if (! class_exists($class) && ! method_exists($class, 'buildQuery')) {
            return GeneralBuild::buildQuery($condition, $queryType);
        }

        return $class::buildQuery($condition, $queryType);
    }

    private function getBuild($build)
    {
        if (! class_exists($build)) {
            foreach (Search::availableBuilds() as $availableBuild) {
                $class = new ReflectionClass($availableBuild);

                if ($class->getShortName() == $build) {
                    return $class->getName();
                }
            }
        }

        return $build;
    }

    public function classNameFromColumnType($tableColumnType): string
    {
        foreach (config('easy-search.autogenerate-query-builds') as $key => $value) {
            if (Str::contains(strtolower($tableColumnType),$key)){
                return $value;
            }
        }

        return 'GeneralBuild';
    }
}

<?php

namespace AppsLab\LaravelEasySearch\Builds;

abstract class AbstractBuild
{
    abstract public static function buildQuery($condition = null, $queryType = null);
}

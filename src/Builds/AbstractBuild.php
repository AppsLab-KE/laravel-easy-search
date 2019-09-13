<?php


namespace AppsLab\LaravelEasySearch\Builds;


abstract class AbstractBuild
{
    abstract static function buildQuery($condition = null, $queryType = null);
}
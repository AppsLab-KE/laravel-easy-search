<?php


namespace AppsLab\LaravelEasySearch\Builds;


class GeneralBuild extends AbstractBuild
{

    static function buildQuery($condition = null, $queryType = null)
    {
        return [
            'query' => $queryType ?? "where",
            'condition' => $condition ?? '',
        ];
    }
}
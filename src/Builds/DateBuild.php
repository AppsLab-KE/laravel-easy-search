<?php


namespace AppsLab\LaravelEasySearch\Builds;


class DateBuild extends AbstractBuild
{

    static function buildQuery($condition = null, $queryType = null)
    {
        return [
            'query' => $queryType ?? 'whereDate',
            'condition' => $condition ?? ''
        ];
    }
}
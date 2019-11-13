<?php

namespace AppsLab\LaravelEasySearch\Builds;

class TextBuild extends AbstractBuild
{
    public static function buildQuery($condition = null, $queryType = null)
    {
        return [
            'query' => $queryType ?? 'orWhere',
            'condition' => $condition ?? 'like',
        ];
    }
}

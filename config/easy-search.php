<?php

return [
    'namespace' => [
        'filter' => 'App\\Http\\Filters',
        'model' => 'App\\',
    ],
    'location' => [
        'filter' => app_path('Http/Filters'),
        'model' => app_path()
    ],
    'autogenerate-query-builds' =>[
        //key is for search and value return the build assigned to it.
        //Add you build and their respective search and you only do this once
        "int" => "IntegerBuild",
        "float" => "IntegerBuild",
        "decimal" => "IntegerBuild",
        "date" => "DateBuild",
        "boolean" => "IntegerBuild",
        "string" => "TextBuild",
        "text" => "TextBuild",
    ],
    'queries' => [
        "where", "whereDate", "whereIn", "whereNotIn","orWhere","whereKey","whereBetween","whereNotBetween","whereNull",
        "whereNotNull","whereMonth","whereDay","whereYear","whereTime","whereColumn",
    ]
];

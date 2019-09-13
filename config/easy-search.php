<?php

return [
    'namespace' => [
        'filter' => 'App\\Http\\Filters',
        'model' => 'App\\',
        // 'decorators' => 'App\\Decorators'
    ],
    'location' => [
        'filter' => app_path('Http/Filters'),
        'model' => app_path()
        // 'decorator' => app_path('Decorators')
    ],
    'autogenerate-query-builds' =>[
        //key is for search and value return the build assigned to it.
        //Add you build and their respective search and you only do this once
        "int" => "IntegerBuild",
        "date" => "DateBuild",
        "index" => "IndexBuild",
    ],
    'queries' => [
        "where", "whereDate", "whereIn", "whereNotIn"
    ]
];
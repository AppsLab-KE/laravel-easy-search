# Laravel Easy Search

[![Latest Version on Packagist](https://img.shields.io/packagist/v/appslabke/laravel-easy-search.svg?style=flat-square)](https://packagist.org/packages/appslabke/laravel-easy-search)
[![Build Status](https://travis-ci.org/AppsLab-KE/laravel-easy-search.svg?branch=master)](https://travis-ci.org/AppsLab-KE/laravel-easy-search)
[![Total Downloads](https://img.shields.io/packagist/dt/appslabke/laravel-easy-search.svg?style=flat-square)](https://packagist.org/packages/appslabke/laravel-easy-search)


This is where your description should go. Try and limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require appslabke/laravel-easy-search
```

## Usage
Add config `vendor:publish --provider="AppsLab\LaravelEasySearch\LaravelEasySearchBaseServiceProvider"`
This will add the config file which you can customize 
`namespace` = allow you to set you filter and module namespace,
`location` = allow to set the location for your filter and model
`autogenerate-query-builds` = This help in autogenerating query build it is used by filter command to auto generate filter query
`queries-types` = this is used to auto complete query type

How to add your own builder.

- Create new service provider `BuildServiceProvider`
- Under boot method add you builds using Search Facede as shown
``` php
Search::builds([
  NameBuild::class
])
```

Don't forget to add your service provider to app.config providers
`App\Providers\BuildServiceProvider::class,`

Working with Laravel Easy Search

- add search on model 

- Global search
  - This allow you to search all the columns in the table.
  - Laravel easy search will search the table only with the filters generated on production but will through an error on local/development.
  - If you want to ignore some columns you can second arguments to ignore the columns.
  ``` php
  ->allowedColumns(['id', 'created_at', 'updated_at'])
  ```
  - by default global search users `search` as the query filter you can replace that by adding the first parameter
  ``` php
  ->searchAllColumns('filter')
  ```
  Make sure you check the 

Add Searchable trait to your model

You can now use Laravel searchable as

``` php
//First make sure you extend Search facade on your controller

use AppsLab\LaravelEasySearch\Facades\Search;

class NameController extends Controller
{
    public index(){
    $results = Search::model(Model::class)
    //Allowed Collumns methods guide you to search only columns added by default if you don't add allowedColumn all solumns will be return
                ->allowedColumns(['name'])
                //Applying filter from your request
                ->applyFilter() //This will search using the filters you generated if filter is not available it will throw a class does not exist error. You can change the filter from the request by adding an array with the key as the filter and the value as the request key
                ->applyFilter(['search_name' => 'name']) This will replace the search_name with name. Name is the filter name
                //Sort by allow you to sort the result pass an array
                ->sortBy('column','ASC')
                //Do you want to add a query? yeah you can do that by building your query connect to the model
                ->buildQuery('whereHas', ['warehouse', function(Builder $query){
                        $query->where('name' , 'like', '%neo%');
                    }])
                //You can add relation addRelation as string or array
                ->addRelation('users')
                // use first to get first item on search
                ->first()

    }
}
```
``` php
use Searchable; 
```

``` php
$skeleton = new Spatie\Skeleton();
echo $skeleton->echoPhrase('Hello, Spatie!');
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email developers@appslab.co.ke instead of using the issue tracker.

## Credits

- [Marvin Collins Hosea](https://github.com/marvinhosea)
- [All Contributors](../../contributors)

## Support us

We are a mixture of technologists, analysts and designers pushing boundaries of what’s possible in problem solving for businesses and communities, and while we are at it we have fun. [Apps:Lab KE](https://appslab.co.ke/)


## Mpesa Contribution

coming soon

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
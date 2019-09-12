<?php


namespace AppsLab\LaravelEasySearch\Decorators;


use AppsLab\LaravelEasySearch\ClassDoesNotExist;
use Illuminate\Support\Str;

class ModelDecorator extends AbstractDecorator
{
    public function __construct()
    {
        $this->setModelNamespace();
    }

    /**
     * Create decorator
     * @param string $modelName
     * @return string
     * @throws
     */
    public function createDecorator(string $modelName)
    {
        $getClass = $this->modelNamespace. Str::studly($modelName);
        if (static::isValidDecorator($getClass) && method_exists($getClass, 'apply')){
            $this->decoratorName = (new $getClass());
            return $this;
        }

        throw new ClassDoesNotExist("{$modelName} model does not exist check confirm from your model dir");
    }

    /**
     * Set model namespace dir
     *
     * @return string
     */
    public function setModelNamespace(): string
    {
        $this->modelNamespace = config('laravel-easy-search.decorators.model-namespace');
    }
}
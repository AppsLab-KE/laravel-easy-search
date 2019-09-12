<?php


namespace AppsLab\LaravelEasySearch\Decorators;



use phpDocumentor\Reflection\Types\String_;

abstract class AbstractDecorator
{
    protected $decoratorName;
    protected $modelNamespace;

    /**
     * Create decorator
     * @param string $modelName
     * @return string
     */
    abstract public function createDecorator(string $modelName);
    abstract public function setModelNamespace(): string;

    public function isValidDecorator($className): bool
    {
        return class_exists($className);
    }
}
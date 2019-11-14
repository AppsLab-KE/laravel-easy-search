<?php

namespace AppsLab\LaravelEasySearch\Contracts;

use Illuminate\Support\Str;

class GeneratorCommand extends \Illuminate\Console\GeneratorCommand
{
    //    protected $dummyImports = '';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        // TODO: Implement getStub() method.
    }

    protected function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace(
            ['DummyNamespace', 'DummyRootNamespace', 'NamespacedDummyUserModel'],
            [$this->getNamespace($name).'\\'.$name['folder'], $this->rootNamespace(), $this->userProviderModel()],
            $stub
        );

        return $this;
    }

    protected function replaceClass($stub, $data)
    {
        $class = str_replace($this->getNamespace($data['name']) . '\\', '', $data['name']);
        $data['condition'] = empty(trim(str_replace(' ', '', $data['condition']))) ? $data['condition'] :
            ", '" . $data['condition'] . "'";
        $value = Str::contains($data['condition'], 'like') ? '"%".$value."%"' : '$value';

        return str_replace(
            ['DummyClass', 'DummyQuery', 'DummyColumn', 'DummyCondition', 'DummyValue'],
            [$class, $data['query'], $data['column'], $data['condition'], $value],
            $stub
        );
    }
}

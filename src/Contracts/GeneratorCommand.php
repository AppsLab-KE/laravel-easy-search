<?php


namespace AppsLab\LaravelEasySearch\Contracts;


class GeneratorCommand extends \Illuminate\Console\GeneratorCommand
{
    protected $dummyCarbon = '';
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
            [$this->getNamespace($name), $this->rootNamespace(), $this->userProviderModel()],
            $stub
        );

        return $this;
    }

    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        return str_replace(
            ['DummyClass', 'DummyCarbon'],
            [$class, $this->dummyCarbon.$this->dummyCarbon == '' ? '' : ";"],
            $stub);
    }
}
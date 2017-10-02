<?php

namespace Laravel\Dusk\Console;

use Illuminate\Console\GeneratorCommand;

class ComponentCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'dusk:component {name : The name of the class}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Dusk component class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Component';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/component.stub';
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace_first($this->rootNamespace(), '', $name);

        return $this->laravel->basePath().'/tests'.str_replace('\\', '/', $name).'.php';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Browser\Components';
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return 'Tests';
    }
}

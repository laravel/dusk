<?php

namespace Laravel\Dusk\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'dusk:page')]
class PageCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'dusk:page {name : The name of the class}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Dusk page class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Page';

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $result = parent::buildClass($name);

        $pageName = $this->argument('name');

        $baseClass = 'Tests\Browser\Pages\Page';

        if (! Str::contains($pageName, '/') && class_exists($baseClass)) {
            return $result;
        } elseif (! class_exists($baseClass)) {
            $baseClass = 'Laravel\Dusk\Page';
        }

        $lineEndingCount = [
            "\r\n" => substr_count($result, "\r\n"),
            "\r" => substr_count($result, "\r"),
            "\n" => substr_count($result, "\n"),
        ];

        $eol = array_keys($lineEndingCount, max($lineEndingCount))[0];

        return str_replace(
            'use Laravel\Dusk\Browser;'.$eol,
            'use Laravel\Dusk\Browser;'.$eol."use {$baseClass};".$eol,
            $result
        );
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/page.stub';
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

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
        return $rootNamespace.'\Browser\Pages';
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

<?php

namespace Minions\Http\Console;

use Orchestra\Canvas\Core\Commands\Generator;
use Symfony\Component\Console\Input\InputOption;

class MakeRpcRequest extends Generator
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'minions:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new RPC Request class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'RPC Request';

    /**
     * Get the stub file for the generator.
     */
    public function getStubFile(): string
    {
        $directory = __DIR__.'/stubs';

        return $this->option('middleware')
            ? "{$directory}/request.middleware.stub"
            : "{$directory}/request.stub";
    }

    /**
     * Get the default namespace for the class.
     */
    public function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace.'\JsonRpc';
    }

    /**
     * Generator options.
     */
    public function generatorOptions(): array
    {
        return [
            'name' => $this->generatorName(),
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['middleware', null, InputOption::VALUE_NONE, 'Request have middleware.'],
        ];
    }
}

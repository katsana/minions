<?php

namespace Minions\Http\Console;

use Orchestra\Canvas\Core\Commands\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeRpcRequest extends GeneratorCommand
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
     *
     * @return string
     */
    protected function getStub()
    {
        $directory = __DIR__.'/stubs';

        return $this->option('middleware')
            ? "{$directory}/request.middleware.stub"
            : "{$directory}/request.stub";

        // return $this->getStubFile();
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
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

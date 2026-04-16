<?php

namespace Minions\Http\Console;

use Orchestra\Canvas\Core\Commands\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeRpcRequest extends GeneratorCommand
{
    /**
     * The generator preset.
     *
     * @var \Orchestra\Canvas\Core\Presets\Preset
     */
    protected $preset;

    /**
     * Create a new command instance.
     *
     * @param  \Orchestra\Canvas\Core\Presets\Preset  $preset
     */
    public function __construct($preset)
    {
        $this->preset = $preset;

        parent::__construct(app('files'));
    }

    /**
     * Resolve the generator preset.
     */
    protected function generatorPreset(): \Orchestra\Canvas\Core\Presets\Preset
    {
        return $this->preset;
    }


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
    protected function getStub()
    {
        $directory = __DIR__.'/stubs';

        return $this->option('middleware')
            ? "{$directory}/request.middleware.stub"
            : "{$directory}/request.stub";
    }

    /**
     * Get the default namespace for the class.
     */
    public function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\JsonRpc';
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

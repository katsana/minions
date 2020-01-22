<?php

namespace Minions\Server\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minions:install-server
        {--force : Overwrite previous installation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Minions Server.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Filesystem $filesystem)
    {
        $routeFile = $this->laravel->basePath('routes/rpc.php');

        if ($files->exists($routeFile) && ! $this->option('force')) {
            $this->error('Route file already exist. Use the --force option to overwrite them.');
        }

        $files->put($routeFile, $files->get(__DIR__.'/stubs/route.stub'));

        $this->info('Route file generated successfully.');
    }
}

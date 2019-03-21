<?php

namespace Minions\Client;

use Clue\React\Buzz\Browser;
use InvalidArgumentException;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;

class Minion
{
    /**
     * Configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * The event-loop implementation.
     *
     * @var \React\EventLoop\LoopInterface
     */
    protected $eventLoop;

    /**
     * List of resolved projects.
     *
     * @var array
     */
    protected $projects = [];

    /**
     * Construct a new Minion.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get Event Loop implementation.
     *
     * @return \React\EventLoop\LoopInterface
     */
    final public function getEventLoop(): LoopInterface
    {
        if (! $this->eventLoop instanceof LoopInterface) {
            $this->eventLoop = Factory::create();
        }

        return $this->eventLoop;
    }

    /**
     * Set Event Loop implementation.
     *
     * @param \React\EventLoop\LoopInterface $eventLoop
     *
     * @return $this
     */
    final public function setEventLoop(LoopInterface $eventLoop): self
    {
        $this->eventLoop = $eventLoop;

        return $this;
    }

    /**
     * Execute the loop.
     *
     * @return void
     */
    final public function run(): void
    {
        $this->getEventLoop()->run();
    }

    /**
     * Create project instance.
     *
     * @param string $project
     *
     * @return \Minions\Client\Project
     */
    public function project(string $project): Project
    {
        if (! isset($this->projects[$project])) {
            $config = $this->projectConfiguration($project);

            $this->projects[$project] = new Project(
                $this->config['id'], $config, $this->createBrowser($config ?? [])
            );
        }

        return $this->projects[$project];
    }

    /**
     * Broadcast message.
     *
     * @param string                           $project
     * @param \Minions\Client\MessageInterface $message
     *
     * @return \React\Promise\PromiseInterface
     */
    public function broadcast(string $project, MessageInterface $message)
    {
        return $this->project($project)->broadcast($message);
    }

    /**
     * Create a new client using factory.
     *
     * @param array $config
     *
     * @return \Clue\React\Buzz\Browser
     */
    protected function createBrowser(array $config): Browser
    {
        return (new Browser($this->getEventLoop()))
                    ->withBase($config['endpoint'])
                    ->withOptions([
                        'timeout' => $config['options']['timeout'] ?? null,
                        'followRedirects' => false,
                        'obeySuccessCode' => true,
                        'streaming' => false,
                    ]);
    }

    /**
     * Get configuration for a project.
     *
     * @param string $project
     *
     * @return array
     */
    protected function projectConfiguration(string $project): array
    {
        if (\is_null($project) || ! \array_key_exists($project, $this->config['projects'])) {
            throw new InvalidArgumentException("Unable to find project [{$project}].");
        }

        return $this->config['projects'][$project];
    }
}

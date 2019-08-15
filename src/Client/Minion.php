<?php

namespace Minions\Client;

use function Clue\React\Block\await;
use function Clue\React\Block\awaitAll;
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
     * @param \React\EventLoop\LoopInterface $eventLoop
     * @param array                          $config
     */
    public function __construct(LoopInterface $eventLoop, array $config)
    {
        $this->eventLoop = $eventLoop;
        $this->config = $config;
    }

    /**
     * Get Event Loop implementation.
     *
     * @return \React\EventLoop\LoopInterface
     */
    final public function getEventLoop(): LoopInterface
    {
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
     * Await for the promises to be resolved.
     *
     * @param array|\React\Promise\PromiseInterface $promises
     *
     * @return array|mixed
     */
    final public function await($promises)
    {
        if (\is_array($promises)) {
            return awaitAll($promises, $this->eventLoop);
        }

        return await($promises, $this->eventLoop);
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
        if (empty($project) || ! isset($this->config['projects'][$project])) {
            throw new InvalidArgumentException("Unable to find project [{$project}].");
        }

        return $this->config['projects'][$project];
    }
}

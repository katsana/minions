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
     */
    public function __construct(LoopInterface $eventLoop, array $config)
    {
        $this->eventLoop = $eventLoop;
        $this->config = \array_merge($config, [
            'enabled' => true,
        ]);
    }

    /**
     * Get Event Loop implementation.
     */
    final public function getEventLoop(): LoopInterface
    {
        return $this->eventLoop;
    }

    /**
     * Set Event Loop implementation.
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
        if ($this->config['enabled'] !== true) {
            return null;
        }

        if (\is_array($promises)) {
            return awaitAll($promises, $this->eventLoop);
        }

        return await($promises, $this->eventLoop);
    }

    /**
     * Execute the loop.
     */
    final public function run(): void
    {
        if ($this->config['enabled'] !== true) {
            return;
        }

        $this->getEventLoop()->run();
    }

    /**
     * Create project instance.
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
     */
    protected function createBrowser(array $config): Browser
    {
        return (new Browser($this->getEventLoop()))
            ->withBase($config['endpoint'])
            ->withOptions([
                'timeout' => $config['options']['timeout'] ?? 60,
                'followRedirects' => false,
                'obeySuccessCode' => true,
                'streaming' => false,
            ]);
    }

    /**
     * Get configuration for a project.
     */
    protected function projectConfiguration(string $project): array
    {
        if (empty($project) || ! isset($this->config['projects'][$project])) {
            throw new InvalidArgumentException("Unable to find project [{$project}].");
        }

        return $this->config['projects'][$project];
    }
}

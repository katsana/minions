<?php

namespace Minions\Client;

use function Clue\React\Block\await;
use function Clue\React\Block\awaitAll;
use Clue\React\Buzz\Browser;
use Clue\React\Mq\Queue;
use InvalidArgumentException;
use Minions\Configuration;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;

class Minion
{
    /**
     * Configuration.
     *
     * @var \Minions\Configuration
     */
    protected $config;

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
    public function __construct(LoopInterface $eventLoop, Configuration $config)
    {
        if (! isset($config['enabled'])) {
            $config['enabled'] = true;
        }

        $this->eventLoop = $eventLoop;
        $this->config = $config;
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
     * Queue for the promises to be resolved.
     */
    final public function queue(string $project, int $concurrency, ?int $limit): Queue
    {
        $project = $this->project($project);

        return new Queue($concurrency, $limit, static function (MessageInterface $message) use ($project) {
            return $project->broadcast($message);
        });
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
        if ($this->enabled() !== true) {
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
        if ($this->enabled() !== true) {
            return;
        }

        $this->getEventLoop()->run();
    }

    /**
     * Check enabled status.
     */
    final public function enabled(): bool
    {
        return $this->config['enabled'];
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

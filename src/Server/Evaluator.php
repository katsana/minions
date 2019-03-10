<?php

namespace Minions\Server;

use Datto\JsonRpc\Evaluator as DattoEvaluator;
use Datto\JsonRpc\Exceptions\MethodException;
use Datto\JsonRpc\Server;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Psr\Http\Message\ServerRequestInterface;

class Evaluator
{
    /**
     * The application implementation.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * List of services.
     *
     * @var array
     */
    protected $services = [];

    /**
     * The request implementation.
     *
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    protected $request;

    public function __construct(Container $container, array $services)
    {
        $this->container = $container;
        $this->services = $services;
    }

    /**
     * Handle the request.
     *
     * @param ServerRequestInterface $request
     *
     * @return
     */
    public function handle(ServerRequestInterface $request)
    {
        $this->request = $request;

        return new Server($this->providesServiceEvaluator());
    }

    protected function providesServiceEvaluator()
    {
        return new class() implements DattoEvaluator {
            /**
             * @param string $method
             * @param array  $arguments
             *
             * @return mixed
             */
            public function evaluate($method, $arguments)
            {
                if (! array_key_exists($method, $this->services)) {
                    throw new MethodException();
                }

                try {
                    return $this->container->make($this->services[$method])->__invoke($arguments);
                } catch (BindingResolutionException $e) {
                    throw new MethodException();
                }
            }
        };
    }
}

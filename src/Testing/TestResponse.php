<?php

namespace Minions\Testing;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Minions\Client\Response;
use RingCentral\Psr7\Response as Psr7Response;
use PHPUnit\Framework\Assert as PHPUnit;

class TestResponse
{
    /**
     * Laravel base test response.
     *
     * @var \Illuminate\Foundation\Testing\TestResponse|\Illuminate\Testing\TestResponse
     */
    protected $baseResponse;

    /**
     * RPC response.
     *
     * @var \Minions\Client\Response
     */
    protected $response;

    /**
     * Create a new test response instance.
     *
     * @param \Illuminate\Foundation\Testing\TestResponse|\Illuminate\Testing\TestResponse $baseResponse
     */
    public function __construct(Psr7Response $psr7Response, $baseResponse)
    {
        $this->response = new Response($psr7Response);
        $this->baseResponse = $baseResponse;
    }

    /**
     * Create a new TestResponse from another response.
     *
     * @param \Illuminate\Http\Response $response
     *
     * @return static
     */
    public static function fromBaseResponse($response)
    {
        $baseResponse = $response->baseResponse;

        return new static(
            new Psr7Response($baseResponse->getStatusCode(), $baseResponse->headers->all(), $baseResponse->getContent()),
            $response
        );
    }

    /**
     * Validate and return the decoded response JSON.
     *
     * @param string|null $key
     *
     * @return mixed
     */
    public function json($key = null)
    {
        if (! \is_null($this->response->getRpcErrorCode())) {
            return Arr::get($this->response->getRpcErrorData(), $key);
        }

        return Arr::get($this->response->getRpcResult(), $key);
    }

    /**
     * Assert that the response has the given JSON validation errors.
     *
     * @param string|array $errors
     *
     * @return $this
     */
    public function assertValidationMissingErrors($keys = null)
    {
        $this->assertHasErrors(-32602, 'The given data was invalid.');

        $jsonErrors = $this->response->getRpcErrorData() ?? [];

        if (empty($jsonErrors)) {
            PHPUnit::assertTrue(true);

            return $this;
        }

        if (\is_null($keys) && \count($jsonErrors) > 0) {
            PHPUnit::fail(
                'Response has unexpected validation errors: '.PHP_EOL.PHP_EOL.
                json_encode($jsonErrors, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            );
        }

        foreach (Arr::wrap($keys) as $key) {
            PHPUnit::assertFalse(
                isset($jsonErrors[$key]),
                "Found unexpected validation error for key: '{$key}'"
            );
        }

        return $this;
    }

    /**
     * Assert that the response has the given JSON validation errors.
     *
     * @param string|array $errors
     *
     * @return $this
     */
    public function assertValidationErrors($errors)
    {
        $this->assertHasErrors(-32602, 'The given data was invalid.');

        $errors = Arr::wrap($errors);

        PHPUnit::assertNotEmpty($errors, 'No validation errors were provided.');

        $jsonErrors = $this->response->getRpcErrorData() ?? [];

        $errorMessage = $jsonErrors
                ? 'Response has the following JSON validation errors:'.
                        PHP_EOL.PHP_EOL.\json_encode($jsonErrors, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL
                : 'Response does not have JSON validation errors.';

        foreach ($errors as $key => $value) {
            PHPUnit::assertArrayHasKey(
                (\is_int($key)) ? $value : $key,
                $jsonErrors,
                "Failed to find a validation error in the response for key: '{$value}'".PHP_EOL.PHP_EOL.$errorMessage
            );

            if (! \is_int($key)) {
                $hasError = false;

                foreach (Arr::wrap($jsonErrors[$key]) as $jsonErrorMessage) {
                    if (Str::contains($jsonErrorMessage, $value)) {
                        $hasError = true;

                        break;
                    }
                }

                if (! $hasError) {
                    PHPUnit::fail(
                        "Failed to find a validation error in the response for key and message: '$key' => '$value'".PHP_EOL.PHP_EOL.$errorMessage
                    );
                }
            }
        }

        return $this;
    }

    /**
     * Assert RPC has errors.
     *
     * @return $this
     */
    public function assertHasErrors(?int $code = null, ?string $message = null)
    {
        $this->baseResponse->assertOk();

        if (\is_null($code) && \is_null($message)) {
            $this->baseResponse->assertJsonStructure(['jsonrpc', 'id', 'error']);
        }

        if (! \is_null($code)) {
            PHPUnit::assertSame($code, $this->response->getRpcErrorCode());
        }

        if (! \is_null($message)) {
            PHPUnit::assertSame($message, $this->response->getRpcErrorMessage());
        }

        return $this;
    }

    /**
     * Assert that the response is successful.
     *
     * @return $this
     */
    public function assertSuccessful()
    {
        $this->assertOk();
        $this->baseResponse->assertJsonStructure(['jsonrpc', 'id', 'result']);

        return $this;
    }

    /**
     * Assert that the response has a not found status code.
     *
     * @return $this
     */
    public function assertNotFound()
    {
        $this->assertHasErrors(-32601, 'Method not found');

        return $this;
    }

    /**
     * Assert that the response has a forbidden status code.
     *
     * @return $this
     */
    public function assertForbidden()
    {
        $this->assertHasErrors(-32601, 'Unauthorized request');

        return $this;
    }

    /**
     * Assert that the response has a 200 status code.
     *
     * @return $this
     */
    public function assertOk()
    {
        $this->baseResponse->assertOk();

        return $this;
    }

    /**
     * Dump the content from the response.
     *
     * @return $this
     */
    public function dump()
    {
        $this->baseResponse->dump();

        return $this;
    }
}

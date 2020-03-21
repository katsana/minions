<?php

namespace Minions\Http;

use Illuminate\Contracts\Validation\Factory;

trait ValidatesRequests
{
    /**
     * Run the validation routine against the given validator.
     *
     * @param \Illuminate\Contracts\Validation\Validator|array $validator
     * @param array|null                                       $arguments
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateWith($validator, $arguments = null): array
    {
        if (\is_array($validator)) {
            $validator = $this->getValidationFactory()->make($arguments, $validator);
        }

        return $validator->validate();
    }

    /**
     * Validate the given request with the given rules.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(
        array $arguments,
        array $rules,
        array $messages = [],
        array $customAttributes = []
    ): array {
        return $this->getValidationFactory()->make(
            $request->all(), $rules, $messages, $customAttributes
        )->validate();
    }

    /**
     * Get a validation factory instance.
     */
    protected function getValidationFactory(): Factory
    {
        return \app(Factory::class);
    }
}

<?php

namespace Minions\Tests\Feature\Http;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Minions\Http\ValidatesRequests;
use Minions\Tests\TestCase;

class ValidatesRequestsTest extends TestCase
{
    use ValidatesRequests;

    /** @test */
    public function it_can_validate_using_validate()
    {
        $this->expectException('Illuminate\Validation\ValidationException');
        $this->expectExceptionMessage('The given data was invalid.');

        $this->validate([
            'email' => 'crynobone[at]katsana.com',
        ], [
            'email' => ['required', 'email'],
            'name' => ['required'],
        ]);
    }

    /** @test */
    public function it_can_validate_using_validate_with()
    {
        $this->expectException('Illuminate\Validation\ValidationException');
        $this->expectExceptionMessage('The given data was invalid.');

        $this->validateWith([
            'email' => ['required', 'email'],
            'name' => ['required'],
        ], [
            'email' => 'crynobone[at]katsana.com',
        ]);
    }


    /** @test */
    public function it_can_validate_using_validate_with_given_validator()
    {
        $this->expectException('Illuminate\Validation\ValidationException');
        $this->expectExceptionMessage('The given data was invalid.');

        $this->validateWith(
            Validator::make([
                'email' => 'crynobone[at]katsana.com',
            ], [
                'email' => ['required', 'email'],
                'name' => ['required'],
            ])
        );
    }
}

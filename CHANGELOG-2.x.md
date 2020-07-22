# Changelog for v2.x

This changelog references the relevant changes (bug and security fixes) done to `katsana/minions`.

## 2.6.0

Released: 2020-07-22

### Added

* Added methods to `Minions\Exceptions\RequestException` to access exception from response object:
    - `getRpcError()`
    - `getRpcErrorCode()`
    - `getRpcErrorMessage()`

## 2.5.0

Released: 2020-05-01

### Added

* Added `Minions\Client\Minion::enabled()` method.
* Added `Minions\Testing\MakesRpcRequests::sendRpc()` method.
* Added `Minions\Testing\TestResponse::assertStatus()` method.

## 2.4.0

Released: 2020-04-20

### Added

* Added `Minions\Client\Response::toArray()` method.

### Changes

* Emulate JSONRPC error when response status code is not `200`, `201`, `203`, `204` or `205`.
* Allow to `serialize()` and `unserialize()` instance of `Minions\Client\Response`.

## 2.3.0

Released: 2020-04-11

### Added

* Added `Minions\Client\Minion::queue()` method.
* Added `Minions\Testing\TestResponse::output()` method.
* Add `Minions\Http\Request::input()` method.

### Changes

* Explicitly require `clue/buzz-react`, `nyholm/psr7` and `symfony/psr-http-message-bridge` to streamline out of the box feature.
* Update minimum `laravie/stream` to `v1.3`+.
* Allow project `token` and `signature` to be set to `null` for app to app communication under private intranet.
* Add `Minions\Exceptions\RequestException::report()` method to send custom error log to Laravel logger. 

## 2.2.1

Released: 2020-04-09

### Fixes

* Make testing client configuration contain `endpoint` to avoid regression issue after moving to new `Minions\Configuration` class.

## 2.2.0

Released: 2020-04-01

### Added

* Added `Minions\Http\Request::handle()` method to resolve and handle RPC request.
* Added `Minions\Http\Request::forwardCallTo()` method to make an internal RPC request.
* Added `Minions\Http\Request::replicateFrom()` method to replicate request with custom parameters.
* Added `Minions\Http\Request::httpMessage()`  method to access `Minions\Http\Message` for the request.

## 2.1.0

Released: 2020-03-27

### Added

* Added `Minions\Configuration` instead of using `array` for configuration.
* Added `minions.config` to Laravel service container.

### Changes

* Add `Minions\Router::routeResolver()` as alias to `Minions\Http\Router::routeResolver()`.

### Deprecated

* Deprecate `Minions\Concerns\Configuration` and will be removed in `v3.0.0`.

## 2.0.0

Released: 2020-03-23

### Added

* Added `Minions\Http\Request` to merge `$arguments` and `$message`.
* Added `Minions\Testing\TestResponse`.

### Changes

* Use `Minions\Exceptions\Exception` on `Minions\Http\Evaluator`.

### Breaking Changes

* Request handler now accept `__invoke(\Minions\Http\Request $request)` instead of `__invoke(array $arguments, \Minions\Http\Message $message)`. This unify the use of `$argument` and `$message` to `Minion\Http\Request`.

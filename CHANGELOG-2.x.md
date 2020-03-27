# Changelog for v2.x

This changelog references the relevant changes (bug and security fixes) done to `katsana/minions`.

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

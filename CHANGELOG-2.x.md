# Changelog for v2.x

This changelog references the relevant changes (bug and security fixes) done to `katsana/minions`.

## 2.0.0

Released: 2020-03-23

### Added

* Added `Minions\Http\Request` to merge `$arguments` and `$message`.
* Added `Minions\Testing\TestResponse`.

### Changes

* Use `Minions\Exceptions\Exception` on `Minions\Http\Evaluator`.

### Breaking Changes

* Request handler now accept `__invoke(\Minions\Http\Request $request)` instead of `__invoke(array $arguments, \Minions\Http\Message $message)`. This unify the use of `$argument` and `$message` to `Minion\Http\Request`.

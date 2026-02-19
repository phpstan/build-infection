# phpstan/build-infection

Custom [Infection](https://infection.github.io/) mutation testing mutators for PHPStan's codebase. These mutators target PHPStan-specific patterns (TrinaryLogic, type comparisons, type specifier contexts) that generic Infection mutators would not cover.

Used as a build dependency by PHPStan repositories (phpstan-src, phpstan-doctrine, phpstan-phpunit, phpstan-deprecation-rules, etc.).

## Mutators

### TrinaryLogicMutator

Swaps `->yes()` with `!->no()` and vice versa, including the inverse (`!->yes()` to `->no()`). Tests that TrinaryLogic checks are precise and not interchangeable.

```diff
- $type->isBoolean()->yes();
+ !$type->isBoolean()->no();
```

### LooseBooleanMutator

Inserts `->toBoolean()` before `->isTrue()`/`->isFalse()` calls. Tests that code distinguishes strict boolean type checks from loose boolean comparisons. Skips calls that already chain through `->toBoolean()`.

```diff
- $type->isFalse()->yes();
+ $type->toBoolean()->isFalse()->yes();
```

### IsSuperTypeOfCalleeAndArgumentMutator

Swaps the callee and argument of `isSuperTypeOf()` calls. Tests that the direction of type relationships matters. Supports variables, property fetches, method calls, static calls, and `new` expressions as both callee and argument.

```diff
- $a->isSuperTypeOf($b);
+ $b->isSuperTypeOf($a);
```

### NonFalseyNonEmptyStringMutator

Swaps `isNonFalseyString()` with `isNonEmptyString()` and vice versa. Tests that code correctly distinguishes between non-falsey strings (excludes `"0"`) and non-empty strings (includes `"0"`).

> **Note:** This mutator is not included in the default config. Consuming repos must add it explicitly via `--mutator-class='PHPStan\Infection\NonFalseyNonEmptyStringMutator'`.

```diff
- $type->isNonFalseyString()->yes();
+ $type->isNonEmptyString()->yes();
```

### TrueTruthyFalseFalseyTypeSpecifierContextMutator

Swaps `true()` with `truthy()` and `false()` with `falsey()` on TypeSpecifierContext (and vice versa). Tests that code correctly handles strict vs loose type specifier contexts.

```diff
- $context->false()
+ $context->falsey()
```

## Usage

1. Check out `build-infection` as a subdirectory of the consuming repo
2. Install its Composer dependencies
3. Generate an `infection.json5` config:

```bash
php build-infection/bin/infection-config.php > infection.json5
```

4. Run Infection with the generated config

### Config generator options

- `--source-directory='path/'` -- add extra source directories
- `--mutator-class='Fully\Qualified\Class'` -- add extra mutator classes
- `--timeout=N` -- override the default timeout (30s)

## Development

Requires PHP ^8.2.

```bash
composer install
make check    # lint, coding standard, tests, PHPStan
make tests    # PHPUnit (unit + .phpt end-to-end tests)
make phpstan  # static analysis (level 8)
```

## License

MIT

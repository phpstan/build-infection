# CLAUDE.md

## Project Overview

`phpstan/build-infection` provides custom [Infection](https://infection.github.io/) mutation testing mutators tailored specifically for PHPStan's codebase. These mutators understand PHPStan-specific patterns (TrinaryLogic, type comparisons, type specifier contexts) that generic Infection mutators would not cover.

This repository is used as a **build dependency** by other PHPStan repositories (phpstan-src, phpstan-doctrine, phpstan-phpunit, phpstan-deprecation-rules, etc.) to run mutation testing on their code with PHPStan-aware mutators.

## Repository Structure

```
bin/
  infection-config.php    # CLI script that generates infection.json5 configs for consuming repos
resources/
  infection.json5         # Default infection config template (mutator list, source dirs, bootstrap)
src/Infection/            # Custom Infection mutator classes
tests/Infection/          # PHPUnit tests for each mutator
tests/phpt/              # End-to-end .phpt tests for the config generator script
```

## Custom Mutators

All mutators live in `src/Infection/` under the `PHPStan\Infection` namespace:

- **TrinaryLogicMutator** - Replaces `->yes()` with `!->no()` and vice versa, testing that TrinaryLogic checks are precise
- **LooseBooleanMutator** - Inserts `->toBoolean()` before `->isTrue()`/`->isFalse()` calls, testing loose vs strict boolean comparison coverage
- **IsSuperTypeOfCalleeAndArgumentMutator** - Swaps callee and argument in `$a->isSuperTypeOf($b)` to `$b->isSuperTypeOf($a)`, testing that type relationship direction matters
- **NonFalseyNonEmptyStringMutator** - Swaps `isNonFalseyString()` with `isNonEmptyString()` and vice versa, testing that the distinction between these is covered
- **TrueTruthyFalseFalseyTypeSpecifierContextMutator** - Swaps `true()`/`truthy()` and `false()`/`falsey()` on TypeSpecifierContext, testing strict vs loose type specifier context handling

## How Consuming Repos Use This

1. The consuming repo checks out `build-infection` as a subdirectory
2. Composer dependencies for build-infection are installed
3. `bin/infection-config.php` generates an `infection.json5` config file that:
   - Disables all default mutators (`@default: false`)
   - Enables only the PHPStan-specific custom mutators
   - Sets `build-infection/vendor/autoload.php` as the bootstrap to load mutator classes
4. The `infection` CLI tool (installed globally via the setup-php composite action at `.github/actions/setup-php/`) runs mutation testing using this config

The config generator supports CLI options:
- `--source-directory='path/'` - Add extra source directories
- `--mutator-class='Fully\Qualified\Class'` - Add extra mutator classes
- `--timeout=N` - Override the default timeout (30s)

## Development

### Requirements

- PHP ^8.2

### Setup

```bash
composer install
```

### Commands (via Makefile)

- `make check` - Run all checks (lint, coding standard, tests, PHPStan)
- `make tests` - Run PHPUnit tests (unit tests + .phpt end-to-end tests)
- `make lint` - Run PHP parallel lint on src/ and tests/
- `make cs` - Check coding standard (requires build-cs, see below)
- `make cs-fix` - Fix coding standard violations
- `make phpstan` - Run PHPStan static analysis (level 8)
- `make infection` - Run Infection mutation testing on this repo itself

### Coding Standard

This project uses [phpstan/build-cs](https://github.com/phpstan/build-cs) (2.x branch) for coding standards. To set it up locally:

```bash
make cs-install
make cs
```

### Code Style

- Tabs for indentation in PHP, XML, and NEON files
- Spaces for indentation in YAML files
- LF line endings
- UTF-8 charset
- See `.editorconfig` for full details

### Testing

Tests are run via PHPUnit with two test suites:
- **Unit tests** (`tests/Infection/*Test.php`) - Test each mutator's `canMutate()` and `mutate()` logic using Infection's `BaseMutatorTestCase`
- **End-to-end tests** (`tests/phpt/*.phpt`) - Test that `bin/infection-config.php` produces correct JSON output

### Static Analysis

PHPStan is configured at level 8 analyzing `bin/`, `src/`, and `tests/` directories.

## CI

GitHub Actions workflows (`.github/workflows/`):

- **build.yml** - Runs on pushes to `1.x` and PRs: lint, coding standard, tests, PHPStan, and mutation testing across PHP 8.2/8.3/8.4
- **tests.yml** - Integration tests that run mutation testing on actual PHPStan repositories (phpstan-src, phpstan-doctrine, phpstan-phpunit, phpstan-deprecation-rules) using this repo's mutators

## Branch

The main development branch is **1.x**.

## License

MIT

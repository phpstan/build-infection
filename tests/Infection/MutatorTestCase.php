<?php declare(strict_types = 1);

namespace PHPStan\Infection;

use Infection\Testing\BaseMutatorTestCase;
use function array_map;
use function method_exists;
use function preg_replace;
use function sprintf;
use function str_starts_with;
use function substr;

abstract class MutatorTestCase extends BaseMutatorTestCase
{

	/**
	 * @param string|string[] $expected
	 */
	final protected function assertMutatesMethodInput(string $input, string|array $expected = []): void
	{
		$this->assertMutatesInput(
			$this->wrapInMethod($input),
			array_map(
                $this->wrapInMethod(...),
                (array) $expected,
            ),
		);
	}

	private function wrapInMethod(string $code): string
	{
		$code = $this->stripOpeningTag($code);

		if (method_exists(BaseMutatorTestCase::class, 'wrapCodeInMethod')) {
			return BaseMutatorTestCase::wrapCodeInMethod($code);
		}

		$indentedCode = preg_replace('/^/m', '        ', $code);

		return sprintf(
			<<<'PHP'
				<?php

				class WrappingClass {
				    public function wrappedTestedCode() {
				%s
				    }
				}
				PHP,
			$indentedCode,
		);
	}

	private function stripOpeningTag(string $code): string
	{
        return str_starts_with($code, '<?php')
            ? (preg_replace('/^\s*\R?/', '', substr($code, 5)) ?? '')
            : $code;
	}

}

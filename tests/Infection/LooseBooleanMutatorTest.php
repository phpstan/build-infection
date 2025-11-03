<?php declare(strict_types = 1);

namespace PHPStan\Infection;

use Infection\Testing\BaseMutatorTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(LooseBooleanMutator::class)]
final class LooseBooleanMutatorTest extends BaseMutatorTestCase
{

	/**
	 * @param string|string[] $expected
	 */
	#[DataProvider('mutationsProvider')]
	public function testMutator(string $input, $expected = []): void
	{
		$this->assertMutatesInput($input, $expected);
	}

	/**
	 * @return iterable<string, array{0: string, 1?: string}>
	 */
	public static function mutationsProvider(): iterable
	{
		yield 'It mutates isFalse() into loose comparison' => [
			<<<'PHP'
				<?php

				$type->isFalse()->yes();
				PHP
,
			<<<'PHP'
				<?php

				$type->toBoolean()->isFalse()->yes();
				PHP
,
		];

		yield 'It mutates isTrue() into loose comparison' => [
			<<<'PHP'
				<?php

				$type->isTrue()->yes();
				PHP
,
			<<<'PHP'
				<?php

				$type->toBoolean()->isTrue()->yes();
				PHP
,
		];

		yield 'It skips already toBoolean() calls to prevent double repetition' => [
			<<<'PHP'
				<?php

				$type->toBoolean()->isTrue()->yes();
				PHP
,
		];

		yield 'It skips non boolean Type calls' => [
			<<<'PHP'
				<?php

				$type->isObject()->yes();
				PHP
,
		];
	}

	protected function getTestedMutatorClassName(): string
	{
		return LooseBooleanMutator::class;
	}

}

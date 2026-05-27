<?php declare(strict_types = 1);

namespace PHPStan\Infection;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(NonFalseyNonEmptyStringMutator::class)]
final class NonFalseyNonEmptyStringMutatorTest extends MutatorTestCase
{

	/**
	 * @param string|string[] $expected
	 */
	#[DataProvider('mutationsProvider')]
	public function testMutator(string $input, $expected = []): void
	{
		$this->assertMutatesMethodInput($input, $expected);
	}

	/**
	 * @return iterable<string, array{0: string, 1?: string}>
	 */
	public static function mutationsProvider(): iterable
	{
		yield 'It mutates isNonEmptyString()' => [
			<<<'PHP'
				<?php

				$type->isNonEmptyString()->yes();
				PHP
,
			<<<'PHP'
				<?php

				$type->isNonFalseyString()->yes();
				PHP
,
		];

		yield 'It mutates isNonFalseyString()' => [
			<<<'PHP'
				<?php

				$type->isNonFalseyString()->yes();
				PHP
,
			<<<'PHP'
				<?php

				$type->isNonEmptyString()->yes();
				PHP
,
		];

		yield 'skip with arguments' => [
			<<<'PHP'
				<?php

				$a->isNonFalseyString($b);
				PHP
,
		];
	}

	protected function getTestedMutatorClassName(): string
	{
		return NonFalseyNonEmptyStringMutator::class;
	}

}

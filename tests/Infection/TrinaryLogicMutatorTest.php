<?php declare(strict_types = 1);

namespace PHPStan\Infection;

use Infection\Testing\BaseMutatorTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(TrinaryLogicMutator::class)]
final class TrinaryLogicMutatorTest extends BaseMutatorTestCase
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
		yield 'It mutates trinary yes' => [
			<<<'PHP'
				<?php
				$trinary = \PHPStan\TrinaryLogic::createYes();
				$trinary->yes();
				PHP
,
			<<<'PHP'
				<?php

				$trinary = \PHPStan\TrinaryLogic::createYes();
				!$trinary->no();
				PHP
,
		];

		yield 'It mutates trinary no' => [
			<<<'PHP'
				<?php
				$trinary = \PHPStan\TrinaryLogic::createYes();
				$trinary->no();
				PHP
,
			<<<'PHP'
				<?php

				$trinary = \PHPStan\TrinaryLogic::createYes();
				!$trinary->yes();
				PHP
,
		];

		yield 'It skips maybe' => [
			<<<'PHP'
				<?php
				$trinary = \PHPStan\TrinaryLogic::createYes();
				$trinary->maybe();
				PHP
,
		];
	}

	protected function getTestedMutatorClassName(): string
	{
		return TrinaryLogicMutator::class;
	}

}

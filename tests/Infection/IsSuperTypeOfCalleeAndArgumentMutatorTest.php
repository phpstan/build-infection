<?php declare(strict_types = 1);

namespace PHPStan\Infection;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(IsSuperTypeOfCalleeAndArgumentMutator::class)]
final class IsSuperTypeOfCalleeAndArgumentMutatorTest extends MutatorTestCase
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
		yield 'It mutates isSuperTypeOf' => [
			<<<'PHP'
				<?php

				$a->isSuperTypeOf($b);
				PHP
,
			<<<'PHP'
				<?php

				$b->isSuperTypeOf($a);
				PHP
,
		];

		yield 'It mutates isSuperTypeOf with property fetch' => [
			<<<'PHP'
				<?php

				$this->a->isSuperTypeOf($b);
				PHP
,
			<<<'PHP'
				<?php

				$b->isSuperTypeOf($this->a);
				PHP
,
		];

		yield 'It mutates isSuperTypeOf with property fetch (reversed)' => [
			<<<'PHP'
				<?php

				$a->isSuperTypeOf($this->b);
				PHP
,
			<<<'PHP'
				<?php

				$this->b->isSuperTypeOf($a);
				PHP
,
		];

		yield 'It mutates isSuperTypeOf with method-call' => [
			<<<'PHP'
				<?php

				$a->isSuperTypeOf($this->call());
				PHP
,
			<<<'PHP'
				<?php

				$this->call()->isSuperTypeOf($a);
				PHP
,
		];

		yield 'It mutates isSuperTypeOf with method-call (reversed)' => [
			<<<'PHP'
				<?php

				$this->call()->isSuperTypeOf($a);
				PHP
,
			<<<'PHP'
				<?php

				$a->isSuperTypeOf($this->call());
				PHP
,
		];

		yield 'It mutates isSuperTypeOf with static method call' => [
			<<<'PHP'
				<?php

				IntegerRangeType::fromInterval(0, null)->isSuperTypeOf($a);
				PHP
,
			<<<'PHP'
				<?php

				$a->isSuperTypeOf(IntegerRangeType::fromInterval(0, null));
				PHP
,
		];

		yield 'It mutates isSuperTypeOf with static method call (reversed)' => [
			<<<'PHP'
				<?php

				$a->isSuperTypeOf(IntegerRangeType::fromInterval(0, null));
				PHP
,
			<<<'PHP'
				<?php

				IntegerRangeType::fromInterval(0, null)->isSuperTypeOf($a);
				PHP
,
		];

		yield 'It mutates isSuperTypeOf with new' => [
			<<<'PHP'
				<?php

				(new ConstantStringType(''))->isSuperTypeOf($delimiterType);
				PHP
,
			<<<'PHP'
				<?php

				$delimiterType->isSuperTypeOf(new ConstantStringType(''));
				PHP
,
		];

		yield 'It mutates isSuperTypeOf with new (reversed)' => [
			<<<'PHP'
				<?php

				$delimiterType->isSuperTypeOf(new ConstantStringType(''));
				PHP
,
			<<<'PHP'
				<?php

				(new ConstantStringType(''))->isSuperTypeOf($delimiterType);
				PHP
,
		];

		yield 'skip isSuperTypeOf with more arguments' => [
			<<<'PHP'
				<?php

				$a->isSuperTypeOf($b, $c);
				PHP
,
		];

		yield 'skip other method calls' => [
			<<<'PHP'
				<?php

				$a->isConstantValue($b);
				PHP
,
		];
	}

	protected function getTestedMutatorClassName(): string
	{
		return IsSuperTypeOfCalleeAndArgumentMutator::class;
	}

}

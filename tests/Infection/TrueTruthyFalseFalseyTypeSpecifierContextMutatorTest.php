<?php declare(strict_types = 1);

namespace PHPStan\Infection;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(TrueTruthyFalseFalseyTypeSpecifierContextMutator::class)]
final class TrueTruthyFalseFalseyTypeSpecifierContextMutatorTest extends MutatorTestCase
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
		yield 'It mutates true()' => [
			<<<'PHP'
				<?php

				$context = TypeSpecifierContext::createNull();
				$context->true();
				PHP
,
			<<<'PHP'
				<?php

				$context = TypeSpecifierContext::createNull();
				$context->truthy();
				PHP
,
		];

		yield 'It mutates truthy()' => [
			<<<'PHP'
				<?php

				$context = TypeSpecifierContext::createNull();
				$context->truthy();
				PHP
,
			<<<'PHP'
				<?php

				$context = TypeSpecifierContext::createNull();
				$context->true();
				PHP
,
		];

		yield 'It mutates false()' => [
			<<<'PHP'
				<?php

				$context = TypeSpecifierContext::createNull();
				$context->false();
				PHP
,
			<<<'PHP'
				<?php

				$context = TypeSpecifierContext::createNull();
				$context->falsey();
				PHP
,
		];

		yield 'It mutates falsey()' => [
			<<<'PHP'
				<?php

				$context = TypeSpecifierContext::createNull();
				$context->falsey();
				PHP
,
			<<<'PHP'
				<?php

				$context = TypeSpecifierContext::createNull();
				$context->false();
				PHP
,
		];

		yield 'It skips null()' => [
			<<<'PHP'
				<?php

				$context = TypeSpecifierContext::createNull();
				$context->null();
				PHP
,
		];
	}

	protected function getTestedMutatorClassName(): string
	{
		return TrueTruthyFalseFalseyTypeSpecifierContextMutator::class;
	}

}

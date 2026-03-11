<?php declare(strict_types = 1);

namespace PHPStan\Infection;

use Infection\Testing\BaseMutatorTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(NodeScopeResolverTestPHPVersionMutator::class)]
final class NodeScopeResolverTestPHPVersionMutatorTest extends BaseMutatorTestCase
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
		yield 'It mutates lint-comment to php7' => [
			<<<'PHP'
				<?php // lint >= 8.0

				$x = 1;
				PHP
,
			<<<'PHP'
				<?php // lint >= 7.4

				$x = 1;
				PHP
,
		];
		/*

		yield 'It mutates lint-comment to previous minor version' => [
			<<<'PHP'
				<?php // lint >= 8.4

				$x = 1;
				PHP
,
			<<<'PHP'
				<?php // lint >= 8.3

				$x = 1;
				PHP
,
		];

		yield 'No mutations when no lint comment' => [
			<<<'PHP'
				<?php declare(strict_types = 1);

				$x = 1;
				PHP
			,
		];
		*/
	}

	protected function getTestedMutatorClassName(): string
	{
		return NodeScopeResolverTestPHPVersionMutator::class;
	}

}

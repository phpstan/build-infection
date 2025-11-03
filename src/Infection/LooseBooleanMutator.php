<?php declare(strict_types = 1);

namespace PHPStan\Infection;

use Infection\Mutator\Definition;
use Infection\Mutator\Mutator;
use Infection\Mutator\MutatorCategory;
use PhpParser\Node;
use function count;
use function in_array;

/**
 * @implements Mutator<Node\Expr\MethodCall>
 */
final class LooseBooleanMutator implements Mutator
{

	public static function getDefinition(): Definition
	{
		return new Definition(
			<<<'TXT'
				Replaces boolean Type->isTrue()/isFalse() with Type->toBoolean()->isTrue()/isFalse() to check loose comparisons coverage.
				TXT
			,
			MutatorCategory::ORTHOGONAL_REPLACEMENT,
			null,
			<<<'DIFF'
				- $type->isFalse()->yes();
				+ $type->isBoolean()->isFalse()->yes();
			DIFF,
		);
	}

	public function getName(): string
	{
		return self::class;
	}

	public function canMutate(Node $node): bool
	{
		if (!$node instanceof Node\Expr\MethodCall) {
			return false;
		}

		if (!$node->name instanceof Node\Identifier) {
			return false;
		}

		if (!in_array($node->name->name, ['isTrue', 'isFalse'], true)) {
			return false;
		}

		if (count($node->getArgs()) !== 0) {
			return false;
		}

		if ($node->var instanceof Node\Expr\MethodCall) {
			if (
				$node->var->name instanceof Node\Identifier
				&& in_array($node->var->name->name, ['toBoolean'], true)
			) {
				return false;
			}
		}

		return true;
	}

	public function mutate(Node $node): iterable
	{
		$node->var = new Node\Expr\MethodCall($node->var, 'toBoolean');
		yield $node;
	}

}

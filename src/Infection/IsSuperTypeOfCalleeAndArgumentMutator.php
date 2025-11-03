<?php declare(strict_types = 1);

namespace PHPStan\Infection;

use Infection\Mutator\Definition;
use Infection\Mutator\Mutator;
use Infection\Mutator\MutatorCategory;
use PhpParser\Node;
use RuntimeException;
use function count;
use function in_array;

/**
 * @implements Mutator<Node\Expr\MethodCall>
 */
final class IsSuperTypeOfCalleeAndArgumentMutator implements Mutator
{

	public static function getDefinition(): Definition
	{
		return new Definition(
			<<<'TXT'
				Replaces the callee and the argument of a isSuperTypeOf() method call.
				TXT
			,
			MutatorCategory::ORTHOGONAL_REPLACEMENT,
			null,
			<<<'DIFF'
				- $a->isSuperTypeOf($b);
				+ $b->isSuperTypeOf($a);
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

		if (
			!$node->var instanceof Node\Expr\Variable
			&& !$node->var instanceof Node\Expr\PropertyFetch
			&& !$node->var instanceof Node\Expr\MethodCall
			&& !$node->var instanceof Node\Expr\StaticCall
			&& !$node->var instanceof Node\Expr\New_
		) {
			return false;
		}

		if (!$node->name instanceof Node\Identifier) {
			return false;
		}

		if (!in_array($node->name->name, ['isSuperTypeOf'], true)) {
			return false;
		}

		$args = $node->getArgs();
		if (count($args) !== 1) {
			return false;
		}

		if (
			!$args[0]->value instanceof Node\Expr\Variable
			&& !$args[0]->value instanceof Node\Expr\PropertyFetch
			&& !$args[0]->value instanceof Node\Expr\MethodCall
			&& !$args[0]->value instanceof Node\Expr\StaticCall
			&& !$args[0]->value instanceof Node\Expr\New_
		) {
			return false;
		}

		return true;
	}

	public function mutate(Node $node): iterable
	{
		$args = $node->getArgs();
		if (count($args) !== 1) {
			throw new RuntimeException();
		}

		yield new Node\Expr\MethodCall(
			$args[0]->value,
			$node->name,
			[new Node\Arg($node->var)],
		);
	}

}

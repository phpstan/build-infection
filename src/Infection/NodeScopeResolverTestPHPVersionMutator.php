<?php declare(strict_types = 1);

namespace PHPStan\Infection;

use Infection\Mutator\Definition;
use Infection\Mutator\Mutator;
use Infection\Mutator\MutatorCategory;
use Infection\PhpParser\Visitor\ParentConnector;
use LogicException;
use PhpParser\Node;
use function in_array;

/**
 * @implements Mutator<Node\Expr\MethodCall|Node\Expr\BooleanNot>
 */
final class NodeScopeResolverTestPHPVersionMutator implements Mutator
{

	public static function getDefinition(): Definition
	{
		return new Definition(
			<<<'TXT'
				Replaces "// lint >= 8.0" conditions in test-files with previous versions.
				TXT
			,
			MutatorCategory::ORTHOGONAL_REPLACEMENT,
			null,
			<<<'DIFF'
				- // lint >= 8.0
				+ // lint >= 7.4
			DIFF,
		);
	}

	public function getName(): string
	{
		return self::class;
	}

	public function canMutate(Node $node): bool
	{
		if ($node instanceof Node\Stmt\Nop) {
			$x=1;
		}

		return true;
	}

	public function mutate(Node $node): iterable
	{
		if ($node instanceof Node\Expr\BooleanNot) {
			$node = $node->expr;
			if (!$node instanceof Node\Expr\MethodCall) {
				throw new LogicException();
			}

			if (!$node->name instanceof Node\Identifier) {
				throw new LogicException();
			}

			if ($node->name->name === 'yes') {
				yield new Node\Expr\MethodCall($node->var, 'no');
			} else {
				yield new Node\Expr\MethodCall($node->var, 'yes');
			}

			return;
		}

		if (!$node->name instanceof Node\Identifier) {
			throw new LogicException();
		}

		if ($node->name->name === 'yes') {
			yield new Node\Expr\BooleanNot(new Node\Expr\MethodCall($node->var, 'no'));
		} else {
			yield new Node\Expr\BooleanNot(new Node\Expr\MethodCall($node->var, 'yes'));
		}
	}

}

<?php declare(strict_types = 1);

namespace PHPStan\Infection;

use Infection\Mutator\Definition;
use Infection\Mutator\Mutator;
use Infection\Mutator\MutatorCategory;
use LogicException;
use PhpParser\Node;
use function count;
use function in_array;

/**
 * @implements Mutator<Node\Expr\MethodCall>
 */
final class NonFalseyNonEmptyStringMutator implements Mutator
{

	public static function getDefinition(): Definition
	{
		return new Definition(
			<<<'TXT'
				Replaces boolean Type->isNonFalseyString() with Type->isNonEmptyString().
				TXT
			,
			MutatorCategory::ORTHOGONAL_REPLACEMENT,
			null,
			<<<'DIFF'
				- $type->isNonFalseyString()->yes();
				+ $type->isNonEmptyString()->yes();
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

		if (!in_array($node->name->name, ['isNonEmptyString', 'isNonFalseyString'], true)) {
			return false;
		}

		if (count($node->getArgs()) !== 0) {
			return false;
		}

		return true;
	}

	public function mutate(Node $node): iterable
	{
		if (!$node->name instanceof Node\Identifier) {
			throw new LogicException();
		}

		if ($node->name->name === 'isNonEmptyString') {
			yield new Node\Expr\MethodCall($node->var, 'isNonFalseyString');
		} else {
			yield new Node\Expr\MethodCall($node->var, 'isNonEmptyString');
		}
	}

}

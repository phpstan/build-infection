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
final class TrueTruthyFalseFalseyTypeSpecifierContextMutator implements Mutator
{

	public static function getDefinition(): Definition
	{
		return new Definition(
			<<<'TXT'
				Replaces boolean TypeSpecifierContext->true() with TypeSpecifierContext->truthy().
				TXT
			,
			MutatorCategory::ORTHOGONAL_REPLACEMENT,
			null,
			<<<'DIFF'
				- $context->false()
				+ $context->falsey()
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

		if (!in_array($node->name->name, ['true', 'truthy', 'false', 'falsey'], true)) {
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

		switch ($node->name->name) {
			case 'true':
				yield new Node\Expr\MethodCall($node->var, 'truthy');
				break;
			case 'truthy':
				yield new Node\Expr\MethodCall($node->var, 'true');
				break;
			case 'false':
				yield new Node\Expr\MethodCall($node->var, 'falsey');
				break;
			case 'falsey':
				yield new Node\Expr\MethodCall($node->var, 'false');
				break;
			default:
				throw new LogicException();
		}
	}

}

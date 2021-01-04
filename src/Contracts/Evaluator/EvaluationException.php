<?php
/**
 * An exception found when evaluating an expression.
 * @package Mathr\Contracts\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Contracts\Evaluator;

use Mathr\Contracts\MathrException;

/**
 * Represents an exception thrown while an expression is evaluated.
 * @package Mathr\Contracts\Evaluator
 */
class EvaluationException extends MathrException
{
    /**
     * The function was not found in memory.
     * @param NodeInterface $node The unknown function's node.
     * @return static The exception to be thrown.
     */
    public static function functionIsNotFound(NodeInterface $node): static
    {
        return new static(
            sprintf("Could not find function '%s' with the given parameters", $node->getData())
        );
    }

    /**
     * A stack memory frame was expected when evaluating a function.
     * @return static The exception to be thrown.
     */
    public static function functionExpectedStackMemory(): static
    {
        return new static("A memory stack frame is expected when evaluating functions");
    }

    /**
     * An unknown operator was detected while evaluating an expression.
     * @param NodeInterface $node The unknown operator detected.
     * @return static The exception to be thrown.
     */
    public static function operatorIsInvalid(NodeInterface $node): static
    {
        return new static(
            sprintf("Could not evaluate unknown operator '%s'", $node->getData())
        );
    }
}

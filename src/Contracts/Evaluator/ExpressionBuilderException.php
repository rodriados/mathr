<?php
/**
 * A general expression builder exception.
 * @package Mathr\Contracts\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Contracts\Evaluator;

use Mathr\Contracts\MathrException;
use Mathr\Contracts\Interperter\TokenInterface;

/**
 * Represents an exception thrown while an expression is built.
 * @package Mathr\Contracts\Evaluator
 */
class ExpressionBuilderException extends MathrException
{
    /**
     * An invalid token type was detected while building an expression.
     * @param TokenInterface $token The invalid token detected while building expression.
     * @return static The exception to be thrown.
     */
    public static function tokenIsInvalid(TokenInterface $token): static
    {
        return new static(
            sprintf("The token '%s' is invalid when building an expression", $token->getData())
        );
    }

    /**
     * A node requested too many arguments from the node stack while building expression.
     * @return static The exception to be thrown.
     */
    public static function argumentsAreNotEnough(): static
    {
        return new static("Not enough nodes found when building the expression");
    }
}

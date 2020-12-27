<?php
/**
 * The basics for an expression builder.
 * @package Mathr\Contracts\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Contracts\Evaluator;

use Mathr\Contracts\Interperter\TokenInterface;

/**
 * Represents an expression builder.
 * @package Mathr\Contracts\Evaluator
 */
interface ExpressionBuilderInterface
{
    /**
     * Retrieves the built expression.
     * @return ExpressionInterface The built expression instance.
     */
    public function getExpression(): ExpressionInterface;

    /**
     * Pushes a token into the expression builder.
     * @param TokenInterface $token The token to be pushed into the expression.
     * @param int|null $argc The number of arguments the token must take.
     */
    public function push(TokenInterface $token, int $argc = null): void;
}

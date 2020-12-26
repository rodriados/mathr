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
     * Pushes a token into the expression builder.
     * @param TokenInterface $token The token to be pushed into the expression.
     */
    public function push(TokenInterface $token): void;
}

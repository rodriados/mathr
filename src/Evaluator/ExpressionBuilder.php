<?php
/**
 * The basic expression builder.
 * @package Mathr\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator;

use Mathr\Contracts\Interperter\TokenInterface;
use Mathr\Contracts\Evaluator\ExpressionBuilderInterface;

/**
 * The expression builder used when parsing an expression.
 * @package Mathr\Evaluator
 */
class ExpressionBuilder implements ExpressionBuilderInterface
{
    /**
     * The expression token stack.
     * @var TokenInterface[] The stack of tokens that represent the expression.
     */
    private array $stack = [];

    /**
     * Pushes a token into the expression builder.
     * @param TokenInterface $token The token to be pushed into the expression.
     */
    public function push(TokenInterface $token): void
    {
        array_push($this->stack, $token);
    }
}

<?php
/**
 * A mock for an expression builder that keeps track of tokens.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */

use Mathr\Contracts\Interperter\TokenInterface;
use Mathr\Contracts\Evaluator\ExpressionInterface;
use Mathr\Contracts\Evaluator\ExpressionBuilderInterface;

/**
 * Mocks an expression builder to simply list all of its pushed tokens.
 * @package Mathr
 */
class TokenListExpressionBuilderMock implements ExpressionBuilderInterface
{
    /**
     * The expression token list.
     * @var TokenInterface[] The list of tokens that represent the expression.
     */
    private array $list = [];

    /**
     * Retrieves the built expression.
     * @return ExpressionInterface The built expression instance.
     */
    public function getExpression(): ExpressionInterface
    {
        throw new RuntimeException('The method is not implemented.');
    }

    /**
     * Gets the list of all tokens pushed into the expression.
     * @return TokenInterface[] The list of pushed tokens.
     */
    public function getTokens(): array
    {
        return $this->list;
    }

    /**
     * Pushes a token into the expression builder.
     * @param TokenInterface $token The token to be pushed into the expression.
     * @param int|null $argc The number of arguments the token must take.
     */
    public function push(TokenInterface $token, int $argc = null): void
    {
        array_push($this->list, $token);
    }
}

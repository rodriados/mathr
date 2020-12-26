<?php
/**
 * A mock for an expression builder that keeps track of tokens.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */

use Mathr\Contracts\Interperter\TokenInterface;
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
     * Pushes a token into the expression builder.
     * @param TokenInterface $token The token to be pushed into the expression.
     */
    public function push(TokenInterface $token): void
    {
        array_push($this->list, $token);
    }

    /**
     * Gets the list of all tokens pushed into the expression.
     * @return TokenInterface[] The list of pushed tokens.
     */
    public function getTokens(): array
    {
        return $this->list;
    }
}

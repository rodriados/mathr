<?php
/**
 * A general parser exception.
 * @package Mathr\Contracts\Interperter
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Contracts\Interperter;

use Mathr\Contracts\MathrException;

/**
 * Represents an exception thrown while an expression is parsed.
 * @package Mathr\Contracts\Interperter
 */
class ParserException extends MathrException
{
    /**
     * A consumed token was unexpected when parsing the expression.
     * @param TokenInterface $token The unexpected token.
     * @return static The exception to be thrown.
     */
    public static function tokenIsUnexpected(TokenInterface $token): static
    {
        return new static(
            sprintf("Unexpected token '%s' at position %d", $token->getData(), $token->getPosition())
        );
    }

    /**
     * The token was mismatched when parsing the expression.
     * @param TokenInterface $token The mismatched token.
     * @return static The exception to be thrown.
     */
    public static function tokenIsMismatched(TokenInterface $token): static
    {
        return new static(
            sprintf("Mismatched token '%s' at position %d", $token->getData(), $token->getPosition())
        );
    }
}

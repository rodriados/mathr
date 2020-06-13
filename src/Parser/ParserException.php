<?php
/**
 * A general parser exception.
 * @package Mathr\Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Parser;

use Mathr\MathrException;

/**
 * Represents a parser exception.
 * @package Mathr\Parser
 */
class ParserException extends MathrException
{
    /**
     * Creates an exception for invalid expression.
     * @return static The exception to be thrown.
     */
    public static function invalidExpression(): static
    {
        return new static("The expression is invalid and could not be parsed.");
    }
}

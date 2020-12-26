<?php
/**
 * A general tokenizer exception.
 * @package Mathr\Contracts\Interperter
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Contracts\Interperter;

use Mathr\Contracts\MathrException;

/**
 * Represents an exception thrown while an expression is tokenized.
 * @package Mathr\Contracts\Interperter
 */
class TokenizerException extends MathrException
{
    /**
     * The expression being parsed is invalid.
     * @return static The exception to be thrown.
     */
    public static function expressionIsInvalid(): static
    {
        return new static(
            "The expression is invalid and could not be parsed."
        );
    }
}

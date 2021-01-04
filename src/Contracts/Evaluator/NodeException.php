<?php
/**
 * A general node exception.
 * @package Mathr\Contracts\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Contracts\Evaluator;

use Mathr\Contracts\MathrException;

/**
 * Represents an exception thrown while a node is created.
 * @package Mathr\Contracts\Evaluator
 */
class NodeException extends MathrException
{
    /**
     * An invalid value was received when a numeric is expected.
     * @param mixed $value The invalid received value.
     * @return static The exception to be thrown.
     */
    public static function numericWasExpected(mixed $value): static
    {
        return new static(
            sprintf("A numeric value is expected but '%s' was received", $value)
        );
    }
}

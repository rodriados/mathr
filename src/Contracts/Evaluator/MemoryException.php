<?php
/**
 * A memory exception.
 * @package Mathr\Contracts\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Contracts\Evaluator;

use Mathr\Contracts\MathrException;

/**
 * Represents a memory exception.
 * @package Mathr\Contracts\Evaluator
 */
class MemoryException extends MathrException
{
    /**
     * Creates an exception to when one tries to put values in immutable memory.
     * @return static The exception to be thrown.
     */
    public static function memoryIsImmutable(): static
    {
        return new static("An immutable memory cannot be changed.");
    }

    /**
     * Creates an exception to when the function call stack is too deep.
     * @return static The exception to be thrown.
     */
    public static function stackOverflow(): static
    {
        return new static("The function-call stack is too deep!");
    }

    /**
     * Creates an exception to when an empty call stack is popped.
     * @return static The exception to be thrown.
     */
    public static function stackIsEmpty(): static
    {
        return new static("The function-call stack is empty.");
    }
}

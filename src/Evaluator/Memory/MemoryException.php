<?php
/**
 * A memory frame exception.
 * @package Mathr\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator\Memory;

use Mathr\MathrException;
use Mathr\Interperter\Node\BindableNodeInterface;

/**
 * Represents a memory frame exception.
 * @package Mathr\Evaluator
 */
class MemoryException extends MathrException
{
    /**
     * Creates an exception to when the function call depth is too deep.
     * @return static The exception to be thrown.
     */
    public static function stackOverflow(): static
    {
        return new static("Function call stack is too deep.");
    }

    /**
     * Creates an exception to when one tries to put values in immutable memory.
     * @return static The exception to be thrown.
     */
    public static function immutableMemory(): static
    {
        return new static("An immutable memory cannot be changed.");
    }

    /**
     * Creates an exception to when a value cannot be bound to a node.
     * @param BindableNodeInterface $bindable The node that rejected binding.
     * @return static The exception to be thrown.
     */
    public static function cannotBind(BindableNodeInterface $bindable): static
    {
        return new static("Invalid value to bind to a {$bindable::class}.");
    }

    /**
     * Creates an exception to when one tries to pop an non existant frame.
     * @return static The exception to be thrown.
     */
    public static function noStackFrame(): static
    {
        return new static("Cannot pop memory frame from empty call stack.");
    }
}

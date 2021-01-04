<?php
/**
 * The basics for a memory stack representation.
 * @package Mathr\Contracts\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Contracts\Evaluator;

/**
 * Represents a memory stack for function calls.
 * @package Mathr\Contracts\Evaluator
 */
interface MemoryStackInterface extends MemoryInterface
{
    /**
     * Pushes scope bindings into a new memory frame.
     * @param NodeInterface[] $bindings The new memory frame scope bindings.
     * @return MemoryStackInterface The newly created memory frame.
     * @throws MemoryException The function call stack is too deep.
     */
    public function framePush(array $bindings): MemoryStackInterface;

    /**
     * Pops the last memory frame from the stack.
     * @throws MemoryException There are no frames to be popped.
     */
    public function framePop(): void;
}

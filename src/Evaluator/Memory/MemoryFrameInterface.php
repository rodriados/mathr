<?php
/**
 * Basic memory frame representation.
 * @package Mathr\Evaluator\Memory
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator\Memory;

use Mathr\Interperter\Node\NodeInterface;

/**
 * Represents a memory frame for function call stacks.
 * @package Mathr\Evaluator\Memory
 */
interface MemoryFrameInterface extends MemoryInterface
{
    /**
     * Pushes scope bindings into a new memory frame.
     * @param NodeInterface ...$args The new memory frame scope bindings.
     * @return MemoryFrameInterface The newly created memory frame.
     * @throws MemoryException The function call stack is too deep.
     */
    public function pushFrame(NodeInterface ...$args): MemoryFrameInterface;

    /**
     * Pops the last memory frame from the stack.
     * @throws MemoryException There are no frames to be popped.
     */
    public function popFrame(): void;
}

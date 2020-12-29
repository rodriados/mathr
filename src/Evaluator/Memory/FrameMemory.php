<?php
/**
 * The bindings for a memory frame.
 * @package Mathr\Evaluator\Memory
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator\Memory;

use Mathr\Evaluator\Memory;
use Mathr\Contracts\Evaluator\NodeInterface;
use Mathr\Contracts\Evaluator\MemoryException;
use Mathr\Contracts\Evaluator\MemoryStackInterface;
use Mathr\Contracts\Evaluator\StorableNodeInterface;

class FrameMemory extends Memory implements MemoryStackInterface
{
    use ImmutableMemory;

    /**
     * FrameMemory constructor.
     * @param MemoryStackInterface $parent The parent memory instance.
     * @param NodeInterface[] $bindings The memory frame's bindings.
     */
    public function __construct(
        MemoryStackInterface $parent,
        private array $bindings = []
    ) {
        parent::__construct($parent);
    }

    /**
     * Retrieves a function or variable from the memory.
     * @param StorableNodeInterface $node The node to retrieve the contents of.
     * @return mixed The value bound to the requested node id.
     */
    public function get(StorableNodeInterface $node): mixed
    {
        return $this->bindings[$node->getStorageId()]
            ?? $this->getParentMemory()->get($node);
    }

    /**
     * Pushes scope bindings into a new memory frame.
     * @param NodeInterface[] $bindings The new memory frame scope bindings.
     * @return MemoryStackInterface The newly created memory frame.
     * @throws MemoryException The function call stack is too deep.
     */
    public function pushFrame(array $bindings): MemoryStackInterface
    {
        if ($this->getParentMemory() instanceof MemoryStackInterface)
            return $this->getParentMemory()->pushFrame($bindings);

        throw MemoryException::stackOverflow();
    }

    /**
     * Pops the last memory frame from the stack.
     * @throws MemoryException There are no frames to be popped.
     */
    public function popFrame(): void
    {
        if ($this->getParentMemory() instanceof MemoryStackInterface)
            $this->getParentMemory()->popFrame();

        throw MemoryException::stackIsEmpty();
    }
}

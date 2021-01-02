<?php
/**
 * The methods implementation for an immutable memory.
 * @package Mathr\Evaluator\Memory
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator\Memory;

use Mathr\Contracts\Evaluator\NodeInterface;
use Mathr\Contracts\Evaluator\MemoryException;
use Mathr\Contracts\Evaluator\StorableNodeInterface;

/**
 * The traits of a memory whose bindings cannot be mutated.
 * @package Mathr\Evaluator\Memory
 */
trait ImmutableMemory
{
    /**
     * Throws an exception, because native memory is immutable.
     * @param StorableNodeInterface $node The node to put the contents of.
     * @param NodeInterface|NodeInterface[] $contents The target node's contents.
     * @throws MemoryException A native memory is immutable.
     */
    public function put(StorableNodeInterface $node, mixed $contents): void
    {
        throw MemoryException::memoryIsImmutable();
    }

    /**
     * Throws an exception, because native memory is immutable.
     * @param StorableNodeInterface $node The node to be removed.
     * @throws MemoryException A native memory is immutable.
     */
    public function delete(StorableNodeInterface $node): void
    {
        throw MemoryException::memoryIsImmutable();
    }
}

<?php
/**
 * The basics for a memory representation.
 * @package Mathr\Contracts\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Contracts\Evaluator;

/**
 * The common operations to all memory types.
 * @package Mathr\Contracts\Evaluator
 */
interface MemoryInterface
{
    /**
     * Retrieves a function or variable from the memory.
     * @param StorableNodeInterface $node The node to retrieve the contents of.
     * @return mixed The value bound to the requested node id.
     */
    public function get(StorableNodeInterface $node): mixed;

    /**
     * Puts the contents of a function or variable into memory.
     * @param StorableNodeInterface $node The node to put the contents of.
     * @param mixed $contents The target node's contents.
     */
    public function put(StorableNodeInterface $node, mixed $contents): void;

    /**
     * Removes a function or variable from the memory.
     * @param StorableNodeInterface $node The node to be removed.
     */
    public function delete(StorableNodeInterface $node): void;

    /**
     * Retrieves the parent memory.
     * @return MemoryInterface|null The current memory's parent.
     */
    public function getParentMemory(): ?MemoryInterface;
}

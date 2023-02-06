<?php
/**
 * The memory for scoped functions and variables.
 * @package Mathr\Evaluator\Memory
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator\Memory;

use Serializable;
use Mathr\Evaluator\Memory;
use Mathr\Contracts\Evaluator\NodeInterface;
use Mathr\Contracts\Evaluator\MemoryInterface;
use Mathr\Contracts\Evaluator\MemoryException;
use Mathr\Contracts\Evaluator\MemoryStackInterface;
use Mathr\Contracts\Evaluator\StorableNodeInterface;

/**
 * Keeps track of a scope's variables and functions.
 * @package Mathr\Evaluator\Memory
 */
class ScopeMemory extends Memory implements MemoryStackInterface, Serializable
{
    /**
     * The function call memory frames.
     * @var FrameMemory[] The list of memory frames managed by the scoped memory.
     */
    private array $frames = [];

    /**
     * The list of scope's variables and functions.
     * @var NodeInterface[] The map of variables and functions.
     */
    private array $mapping = [];

    /**
     * ScopedMemory constructor.
     * @param MemoryInterface|null $parent The parent memory instance.
     * @param int $maxDepth The maximum function-call depth of the new memory frame.
     */
    public function __construct(
        ?MemoryInterface $parent = null,
        private int $maxDepth = 20,
    ) {
        parent::__construct($parent);
    }

    /**
     * Exports the contents of the memory for serialization.
     * @return array The exported memory's contents.
     */
    public function __serialize(): array
    {
        return [ $this->mapping, $this->maxDepth ];
    }

    /**
     * Unserializes a memory from its contents.
     * @param array $data The memory's contents.
     */
    public function __unserialize(array $data): void
    {
        [ $this->mapping, $this->maxDepth ] = $data;
    }

    /**
     * Retrieves a function or variable from the memory.
     * @param StorableNodeInterface $node The node to retrieve the contents of.
     * @return mixed The value bound to the requested node id.
     */
    public function get(StorableNodeInterface $node): mixed
    {
        return $this->mapping[$node->getStorageId()]
            ?? $this->getParentMemory()?->get($node);
    }

    /**
     * Puts the contents of a function or variable into memory.
     * @param StorableNodeInterface $node The node to put the contents of.
     * @param mixed $contents The target node's contents.
     */
    public function put(StorableNodeInterface $node, mixed $contents): void
    {
        $this->mapping[$node->getStorageId()] = $contents;
    }

    /**
     * Removes a function or variable from the memory.
     * @param StorableNodeInterface $node The node to be removed.
     */
    public function delete(StorableNodeInterface $node): void
    {
        unset($this->mapping[$node->getStorageId()]);
    }

    /**
     * Pushes scope bindings into a new memory frame.
     * @param NodeInterface[] $bindings The new memory frame scope bindings.
     * @return MemoryStackInterface The newly created memory frame.
     * @throws MemoryException The function call stack is too deep.
     */
    public function framePush(array $bindings): MemoryStackInterface
    {
        if (count($this->frames) > $this->maxDepth)
            throw MemoryException::stackOverflow();

        array_push($this->frames, $frame = new FrameMemory($this, $bindings));
        return $frame;
    }

    /**
     * Pops the last memory frame from the stack.
     * @throws MemoryException There are no frames to be popped.
     */
    public function framePop(): void
    {
        if (empty($this->frames))
            throw MemoryException::stackIsEmpty();

        array_pop($this->frames);
    }

    /**
     * Exports the memory's mappings by serializing it into a string.
     * @return string The serialized memory's mappings string.
     */
    public function serialize(): string
    {
        return serialize([
            $this->mapping,
            $this->maxDepth,
        ]);
    }

    /**
     * Imports a node by unserializing it from a string.
     * @param string $serialized The serialized node string.
     */
    public function unserialize($serialized): void
    {
        [ $this->mapping, $this->maxDepth ] = unserialize($serialized);
    }
}

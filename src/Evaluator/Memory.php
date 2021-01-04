<?php
/**
 * The base of every memory type.
 * @package Mathr\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator;

use Mathr\Contracts\Evaluator\MemoryInterface;

/**
 * The abstract base for all memory types.
 * @package Mathr\Evaluator
 */
abstract class Memory implements MemoryInterface
{
    /**
     * Memory constructor.
     * @param MemoryInterface|null $parent The parent memory instance.
     */
    public function __construct(
        protected ?MemoryInterface $parent = null
    ) {}

    /**
     * Retrieves the parent memory.
     * @return MemoryInterface|null The current memory's parent.
     */
    public function getParentMemory(): ?MemoryInterface
    {
        return $this->parent;
    }

    /**
     * Changes the parent memory instance.
     * @param MemoryInterface|null $parent The new parent memory.
     */
    public function setParentMemory(?MemoryInterface $parent): void
    {
        $this->parent = $parent;
    }
}

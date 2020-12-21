<?php
/**
 * The base of every memory type.
 * @package Mathr\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator\Memory;

/**
 * The abstract base for different memory types.
 * @package Mathr\Evaluator
 */
abstract class Memory implements MemoryInterface
{
    /**
     * Memory constructor.
     * @param Memory|null $parent The parent memory frame.
     */
    public function __construct(
        protected ?Memory $parent = null
    ) {}

    /**
     * Retrieves the parent memory.
     * @return Memory|null The current memory's parent.
     */
    public function getParentMemory(): ?Memory
    {
        return $this->parent;
    }
}

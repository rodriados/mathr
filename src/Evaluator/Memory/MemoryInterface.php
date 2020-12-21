<?php
/**
 * Basic memory representation.
 * @package Mathr\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator\Memory;

use Mathr\Parser\Node\BindableNodeInterface;
use Mathr\Parser\Node\EvaluableNodeInterface;

/**
 * The common operations to all memory types.
 * @package Mathr\Evaluator
 */
interface MemoryInterface
{
    /**
     * Retrieves a function or variable from the memory.
     * @param BindableNodeInterface $node The node to retrieve the contents of.
     * @return BindableNodeInterface|null The node bound to the respective contents.
     */
    public function get(BindableNodeInterface $node): ?BindableNodeInterface;

    /**
     * Puts the contents of a function or variable into memory.
     * @param BindableNodeInterface $node The node to put the contents of.
     * @param EvaluableNodeInterface $contents The target node's contents.
     */
    public function put(BindableNodeInterface $node, EvaluableNodeInterface $contents): void;

    /**
     * Removes a function or variable from the memory.
     * @param BindableNodeInterface $node The node to be removed.
     */
    public function delete(BindableNodeInterface $node): void;

    /**
     * Retrieves the parent memory.
     * @return Memory|null The current memory's parent.
     */
    public function getParentMemory(): ?MemoryInterface;
}

<?php
/**
 * Nodes that can be bound to a native structure.
 * @package Mathr\Parser\Node
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Parser\Node;

use Mathr\Evaluator\Memory\MemoryException;

/**
 * Represents the minimal needed by a node that can be bound.
 * @package Mathr\Parser\Node
 */
interface BindableNodeInterface extends EvaluableNodeInterface
{
    /**
     * Binds the node to a target value.
     * @param mixed $target The value to be bound to the node.
     * @throws MemoryException The node binding was rejected.
     */
    public function bind(mixed $target): void;

    /**
     * Checks whether the node is bound to a value.
     * @return bool Is the node bound?
     */
    public function isBound(): bool;
}

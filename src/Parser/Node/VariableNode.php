<?php
/**
 * Node for variables.
 * @package Mathr\Parser\Node
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Parser\Node;

use Mathr\Evaluator\Memory\MemoryException;
use Mathr\Evaluator\Memory\MemoryInterface;

/**
 * Stores a variable reference in an expression node.
 * @package Mathr\Parser\Node
 */
class VariableNode extends Node implements BindableNodeInterface
{
    /**
     * The value the variable is bound to.
     * @var NumberNode|null The current node binding.
     */
    private ?NumberNode $binding = null;

    /**
     * Represents the node as a string.
     * @return string The node's string representation.
     */
    public function __toString(): string
    {
        return '$' . $this->getData();
    }

    /**
     * Binds the node to a target value.
     * @param mixed $target The value to be bound to the node.
     * @throws MemoryException The node binding was rejected.
     */
    public function bind(mixed $target): void
    {
        if ($target instanceof NumberNode) {
            $this->binding = $target;
        } elseif (is_numeric($target)) {
            $this->binding = NumberNode::make($target);
        } else {
            throw MemoryException::cannotBind($this);
        }
    }

    /**
     * Checks whether the node is bound to a value.
     * @return bool Is the node bound?
     */
    public function isBound(): bool
    {
        return !is_null($this->binding);
    }

    /**
     * Evaluates the node, possibly into a value.
     * @param MemoryInterface $memory The memory for functions and variables.
     * @return NodeInterface The resulting evaluation node.
     */
    public function evaluate(MemoryInterface $memory): NodeInterface
    {
        // TODO: Implement evaluate() method.
    }
}

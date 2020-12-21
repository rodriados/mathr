<?php
/**
 * Node for functions.
 * @package Mathr\Parser\Node
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Parser\Node;

use Mathr\Evaluator\Memory\MemoryException;
use Mathr\Evaluator\Memory\MemoryInterface;

/**
 * Stores a function reference in an expession node.
 * @package Mathr\Parser\Node
 */
class FunctionNode extends ParenthesisNode implements BindableNodeInterface
{
    /**
     * Keeps track of the number of arguments passed to function.
     * @var int The total amount of function arguments.
     */
    protected int $argCount = 0;

    /**
     * The function's bindings.
     * @var mixed The current node bindings.
     */
    private mixed $bindings = null;

    /**
     * Represents the node as a string.
     * @return string The node's string representation.
     */
    public function __toString(): string
    {
        return $this->getData() . "@{$this->argCount}";
    }

    /**
     * Retrieves the data represented by the node.
     * @return string The node's internal data.
     */
    public function getData(): string
    {
        return rtrim(parent::getData(), "(");
    }

    /**
     * Increments the total amount of function arguments.
     * @return int The incremented number of arguments.
     */
    public function argIncrement(): int
    {
        return ++$this->argCount;
    }

    /**
     * Binds the node to a target value.
     * @param mixed $target The value to be bound to the node.
     * @throws MemoryException The given target cannot be bound to a function.
     */
    public function bind(mixed $target): void
    {
        if (is_callable($target) || is_array($target)) {
            $this->bindings = $target;
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
        return !is_null($this->bindings);
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

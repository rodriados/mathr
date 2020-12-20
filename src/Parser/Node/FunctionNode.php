<?php
/**
 * Node for functions.
 * @package Mathr\Parser\Node
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Parser\Node;

/**
 * Stores a function reference in an expession node.
 * @package Mathr\Parser\Node
 */
class FunctionNode extends ParenthesisNode
{
    /**
     * Keeps track of the number of arguments passed to function.
     * @var int The total amount of function arguments.
     */
    protected int $argCount = 0;

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
}

<?php
/**
 * Node for numeric values.
 * @package Mathr\Evaluator\Node
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator\Node;

use Mathr\Evaluator\Node;
use Mathr\Interperter\Token;
use Mathr\Contracts\Evaluator\NodeException;
use Mathr\Contracts\Evaluator\NodeInterface;
use Mathr\Contracts\Evaluator\MemoryInterface;

/**
 * Represents a numeric value in an expression node.
 * @package Mathr\Evaluator\Node
 */
class NumberNode extends Node
{
    /**
     * Evaluates the node and produces a result.
     * @param MemoryInterface $memory The memory to lookup for bindings.
     * @return NodeInterface The produced resulting node.
     */
    public function evaluate(MemoryInterface $memory): NodeInterface
    {
        // A number node is, by itself, the final product of any expression we
        // might want to evaluate. Therefore, we simply return the node here.
        return $this;
    }

    /**
     * Represents the node as a string.
     * @return string The node's string representation.
     */
    public function strRepr(): string
    {
        static $regex = '/^([-+])?0*([1-9][0-9]*|0)(?:(?:(\.[0-9]*[1-9])|\.)0*)?$/';

        if (preg_match($regex, $this->getData(), $match))
            return self::joinNumber(array_slice($match, 1));

        return $this->getData();
    }

    /**
     * Creates a new node from a numeric value.
     * @param int|float|string $value The value to create the node with.
     * @return static The created node.
     * @throws NodeException A numeric value was expected.
     */
    public static function make(int|float|string $value): static
    {
        return is_numeric($value)
            ? new static(new Token($value, type: Token::NUMBER))
            : throw NodeException::numericWasExpected($value);
    }

    /**
     * Joins the number's parts together, taking care of possible negative zero.
     * @param string[] $number The number's parts to be joined together.
     * @return string The final joined number.
     */
    private static function joinNumber(array $number): string
    {
        return $number != [ '-', '0' ]
            ? join($number)
            : '0';
    }
}

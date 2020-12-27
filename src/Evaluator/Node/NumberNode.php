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

/**
 * Represents a numeric value in an expression node.
 * @package Mathr\Evaluator\Node
 */
class NumberNode extends Node
{
    /**
     * Creates a new node from a numeric value.
     * @param int|float|string $value The value to create the node with.
     * @return static The created node.
     */
    public static function make(int|float|string $value): static
    {
        return is_numeric($value)
            ? new static(new Token($value, type: Token::NUMBER))
            : throw NodeException::numericWasExpected($value);
    }
}

<?php
/**
 * Node for numeric values.
 * @package Mathr\Parser\Node
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Parser\Node;

use Mathr\Parser\Token;

/**
 * Stores a numeric value in an expression node.
 * @package Mathr\Parser\Node
 */
class NumberNode extends Node
{
    /**
     * Creates a new node from a numeric value.
     * @param int|float $value The value to create the node with.
     * @return static The created node.
     */
    public static function make(int|float $value): static
    {
        $token = new Token($value, type: Token::NUMBER);
        return new static($token);
    }
}

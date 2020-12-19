<?php
/**
 * Node for variables.
 * @package Mathr\Parser\Node
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Parser\Node;

/**
 * Stores a variable reference in an expression node.
 * @package Mathr\Parser\Node
 */
class VariableNode extends Node
{
    /**
     * Represents the node as a string.
     * @return string The node's string representation.
     */
    public function __toString(): string
    {
        return '$' . $this->getData();
    }
}

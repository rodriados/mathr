<?php
/**
 * Basic node representation.
 * @package Mathr\Parser\Node
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Parser\Node;

use Mathr\Parser\Token;

/**
 * Represents the minimal needed by a node.
 * @package Mathr\Parser\Node
 */
interface NodeInterface
{
    /**
     * Represents the node as a string.
     * @return string The node's string representation.
     */
    public function __toString(): string;

    /**
     * Retrieves the data represented by the node.
     * @return string The node's internal data.
     */
    public function getData(): string;

    /**
     * Returns the token represented by the node.
     * @return Token The node's representation token.
     */
    public function getToken(): Token;
}

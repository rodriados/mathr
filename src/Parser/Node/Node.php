<?php
/**
 * Base for all general node types.
 * @package Mathr\Parser\Node
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Parser\Node;

use Mathr\Parser\Token;

/**
 * Represents the most general node type.
 * @package Mathr\Parser\Node
 */
abstract class Node implements NodeInterface
{
    /**
     * Node constructor.
     * @param Token $token The token represented by the node.
     */
    public function __construct(
        public Token $token
    ) {}

    /**
     * Represents the node as a string.
     * @return string The node's string representation.
     */
    public function __toString(): string
    {
        return $this->getData();
    }

    /**
     * Retrieves the data represented by the node.
     * @return string The node's internal data.
     */
    public function getData(): string
    {
        return $this->getToken()->getData();
    }

    /**
     * Returns the token represented by the node.
     * @return Token The node's representation token.
     */
    final public function getToken(): Token
    {
        return $this->token;
    }
}

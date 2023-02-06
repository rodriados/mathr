<?php
/**
 * General base for all node types.
 * @package Mathr\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator;

use Mathr\Interperter\Token;
use Mathr\Contracts\Evaluator\NodeInterface;
use Mathr\Contracts\Interperter\TokenInterface;

/**
 * Represents the most general node type.
 * @package Mathr\Evaluator
 */
abstract class Node implements NodeInterface
{
    /**
     * Node constructor.
     * @param TokenInterface $token The token represented by the node.
     */
    public function __construct(
        protected TokenInterface $token
    ) {}

    /**
     * Represents the node as a string.
     * @return string The node's string representation.
     */
    final public function __toString(): string
    {
        return $this->strRepr();
    }

    /**
     * Exports the components of a node for serialization.
     * @return array The exported node's components.
     */
    public function __serialize(): array
    {
        return [ $this->token->serialize() ];
    }

    /**
     * Unserializes a node from its components.
     * @param array $data The node's components.
     */
    public function __unserialize(array $data): void
    {
        [ $token ] = $data;
        $this->unserialize($token);
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
     * @return TokenInterface The node's representation token.
     */
    final public function getToken(): TokenInterface
    {
        return $this->token;
    }

    /**
     * Represents the node as a string.
     * @return string The node's string representation.
     */
    public function strRepr(): string
    {
        return $this->getData();
    }

    /**
     * Exports the node by serializing it into a string.
     * @return string The serialized node string.
     */
    public function serialize(): string
    {
        return $this->token->serialize();
    }

    /**
     * Imports a node by unserializing it from a string.
     * @param string $serialized The serialized node string.
     */
    public function unserialize(string $serialized): void
    {
        $this->token = new Token();
        $this->token->unserialize($serialized);
    }
}

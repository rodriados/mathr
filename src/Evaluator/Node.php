<?php
/**
 * General base for all node types.
 * @package Mathr\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator;

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
}

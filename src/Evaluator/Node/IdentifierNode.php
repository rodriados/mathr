<?php
/**
 * Node for generic identifiers.
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
use Mathr\Contracts\Evaluator\StorableNodeInterface;

/**
 * Represents an identifier reference in an expression node.
 * @package Mathr\Evaluator\Node
 */
class IdentifierNode extends Node implements StorableNodeInterface
{
    /**
     * Informs the node's storage id for lookup and storage in memories.
     * @return string The node's storage id.
     */
    public function getStorageId(): string
    {
        return "\${$this->getData()}";
    }

    /**
     * Evaluates the node and produces a result.
     * @param MemoryInterface $memory The memory to lookup for bindings.
     * @return NodeInterface The produced resulting node.
     * @throws NodeException The identifier was bound to a non-numeric value.
     */
    public function evaluate(MemoryInterface $memory): NodeInterface
    {
        $binding = $memory->get($this);

        if ($binding instanceof NodeInterface)
            return $binding->evaluate($memory);

        if (is_numeric($binding))
            return NumberNode::make($binding);

        return $this;
    }

    /**
     * Creates a new node from an identifier.
     * @param string $name The name of the identifier to create the node for.
     * @return static The created node.
     */
    public static function make(string $name): static
    {
        $token = new Token($name, type: Token::IDENTIFIER);
        return new static($token);
    }
}

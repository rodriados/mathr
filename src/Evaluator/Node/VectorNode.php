<?php
/**
 * Node for vectors.
 * @package Mathr\Evaluator\Node
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator\Node;

use Mathr\Interperter\Token;
use Mathr\Contracts\Evaluator\NodeInterface;
use Mathr\Contracts\Evaluator\MemoryInterface;

/**
 * Represents a vector in an expression node.
 * @package Mathr\Evaluator\Node
 */
class VectorNode extends HierarchyNode
{
    /**
     * Retrieves the data represented by the node.
     * @return string The node's internal data.
     */
    public function getData(): string
    {
        return sprintf('{}@%d', $this->getHierarchyCount());
    }

    /**
     * Looks up and retrieve for an element in the vector.
     * @param int $index The index of element node to lookup for.
     * @return NodeInterface|null The retrieved element node.
     */
    public function getElement(int $index): ?NodeInterface
    {
        return $index < $this->getHierarchyCount()
            ? $this->getHierarchy()[$index]
            : null;
    }

    /**
     * Evaluates the node and produces a result.
     * @param MemoryInterface $memory The memory to lookup for bindings.
     * @return NodeInterface The produced resulting node.
     */
    public function evaluate(MemoryInterface $memory): NodeInterface
    {
        return static::make($this->evaluateHierarchy($memory));
    }

    /**
     * Represents the node as a string.
     * @return string The node's string representation.
     */
    public function strRepr(): string
    {
        $hierarchy = $this->getHierarchy();
        return sprintf('{%s}', static::strHierarchy($hierarchy));
    }

    /**
     * Creates a new node from the vector's contents.
     * @param NodeInterface[] $args The vector's contents.
     * @return static The created node.
     */
    public static function make(array $args): static
    {
        $token = new Token('{', type: Token::CURLYBRACES);
        return new static($token, $args);
    }
}

<?php
/**
 * Generic hierarchy node.
 * @package Mathr\Evaluator\Node
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator\Node;

use Mathr\Evaluator\Node;
use Mathr\Contracts\Evaluator\NodeInterface;
use Mathr\Contracts\Evaluator\MemoryInterface;
use Mathr\Contracts\Interperter\TokenInterface;

/**
 * Represents a generic node with hierarchical children.
 * @package Mathr\Evaluator\Node
 */
abstract class HierarchyNode extends Node
{
    /**
     * HierarchyNode constructor.
     * @param TokenInterface $token The token represented by the node.
     * @param NodeInterface[] $hierarchy The node's hierarchical children.
     */
    public function __construct(
        TokenInterface $token,
        protected array $hierarchy = []
    ) {
        parent::__construct($token);
    }

    /**
     * Gives access to the node's hierarchy.
     * @return NodeInterface[] The node's hierarchical children.
     */
    public function getHierarchy(): array
    {
        return $this->hierarchy;
    }

    /**
     * Informs the node's total number of nodes in hierarchy.
     * @return int The node's hierarchy count.
     */
    public function getHierarchyCount(): int
    {
        return count($this->getHierarchy());
    }

    /**
     * Evaluates all nodes in hierarchy and return their results.
     * @param MemoryInterface $memory The memory to lookup for bindings.
     * @return NodeInterface[] The produced resulting nodes.
     */
    protected function evaluateHierarchy(MemoryInterface $memory): array
    {
        $lambda = fn (NodeInterface $child) => $child->evaluate($memory);
        return array_map($lambda, $this->getHierarchy());
    }

    /**
     * Creates the joined string representation of nodes in hierarchy.
     * @param NodeInterface[] $hierarchy The node's hierarchy.
     * @return string The hierarchy's string representation.
     */
    protected static function strHierarchy(array $hierarchy): string
    {
        $lambda = fn (NodeInterface $node) => $node->strRepr();
        return join(', ', array_map($lambda, $hierarchy));
    }

    /**
     * Checks whether all nodes on the given list are number nodes.
     * @param NodeInterface[] $nodes The list of nodes to be checked.
     * @return bool Do all nodes on the list represent a number?
     */
    final protected static function allOfNumbers(array $nodes): bool
    {
        $lambda = fn (bool $carry, NodeInterface $node) => $carry && (
            $node instanceof NumberNode ||
            $node instanceof VectorNode && self::allOfNumbers($node->getHierarchy())
        );

        return array_reduce($nodes, $lambda, true);
    }
}

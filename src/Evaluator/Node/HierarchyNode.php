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
     * @param NodeInterface[] $children The node's argument list.
     */
    public function __construct(
        TokenInterface $token,
        protected array $children = []
    ) {
        parent::__construct($token);
    }

    /**
     * Gives access to the node's children.
     * @return NodeInterface[] The list of children.
     */
    protected function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Informs the node's total number of children.
     * @return int The node's children count.
     */
    protected function getChildrenCount(): int
    {
        return count($this->getChildren());
    }

    /**
     * Creates the joined string representation of many nodes.
     * @param NodeInterface[] $children The node's children.
     * @return string The children's string representation.
     */
    protected function strJoin(array $children): string
    {
        $mapper = fn (NodeInterface $node) => $node->strRepr();
        return join(', ', array_map($mapper, $children));
    }
}

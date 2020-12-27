<?php
/**
 * Node for brackets operator.
 * @package Mathr\Evaluator\Node
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator\Node;

/**
 * Represents the brackets operator in an expression node.
 * @package Mathr\Evaluator\Node
 */
class BracketsNode extends HierarchyNode
{
    /**
     * Retrieves the data represented by the node.
     * @return string The node's internal data.
     */
    public function getData(): string
    {
        return "[]@{$this->getChildrenCount()}";
    }

    /**
     * Represents the node as a string.
     * @return string The node's string representation.
     */
    public function strRepr(): string
    {
        $children = $this->getChildren();
        $vector = array_shift($children);
        return "{$vector->strRepr()}[{$this->strJoin($children)}]";
    }
}

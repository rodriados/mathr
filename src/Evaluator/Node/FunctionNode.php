<?php
/**
 * Node for functions.
 * @package Mathr\Evaluator\Node
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator\Node;

use Mathr\Contracts\Evaluator\StorableNodeInterface;

/**
 * Represents a function in an expression node.
 * @package Mathr\Evaluator\Node
 */
class FunctionNode extends HierarchyNode implements StorableNodeInterface
{
    /**
     * Retrieves the data represented by the node.
     * @return string The node's internal data.
     */
    public function getData(): string
    {
        return substr($this->token->getData(), 0, -1);
    }

    /**
     * Informs the node's storage id for lookup and storage in memories.
     * @return string The node's storage id.
     */
    public function getStorageId(): string
    {
        return sprintf('%s@%d', $this->getData(), $this->getChildrenCount());
    }

    /**
     * Represents the node as a string.
     * @return string The node's string representation.
     */
    public function strRepr(): string
    {
        $children = $this->getChildren();
        return sprintf('%s(%s)', $this->getData(), $this->strJoin($children));
    }
}

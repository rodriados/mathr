<?php
/**
 * Nodes that can be evaluated into a value.
 * @package Mathr\Parser\Node
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Parser\Node;

use Mathr\Evaluator\Memory\MemoryInterface;

/**
 * Represents the minimal needed by a node that can be evaluated.
 * @package Mathr\Parser\Node
 */
interface EvaluableNodeInterface extends NodeInterface
{
    /**
     * Evaluates the node, possibly into a value.
     * @param MemoryInterface $memory The memory for functions and variables.
     * @return NodeInterface The resulting evaluation node.
     */
    public function evaluate(MemoryInterface $memory): NodeInterface;
}

<?php
/**
 * An evaluable expression.
 * @package Mathr\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator;

use Mathr\Contracts\Evaluator\NodeInterface;
use Mathr\Contracts\Evaluator\ExpressionInterface;

/**
 * Represents an evaluable expression, the root of a tree of nodes.
 * @package Mathr\Evaluator
 */
class Expression implements ExpressionInterface
{
    /**
     * Expression constructor.
     * @param NodeInterface $root The expression's root node.
     */
    public function __construct(
        protected NodeInterface $root
    ) {}

    /**
     * Represents the node as a string.
     * @return string The node's string representation.
     */
    public function strRepr(): string
    {
        return $this->root->strRepr();
    }
}

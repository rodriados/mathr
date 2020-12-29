<?php
/**
 * The basics for an evaluable expression.
 * @package Mathr\Contracts\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Contracts\Evaluator;

/**
 * Represents an evaluable expression.
 * @package Mathr\Contracts\Evaluator
 */
interface ExpressionInterface
{
    /**
     * Evaluates the node and produces a result.
     * @param MemoryInterface $memory The memory to lookup for bindings.
     * @return NodeInterface The produced resulting node.
     */
    public function evaluate(MemoryInterface $memory): NodeInterface;

    /**
     * Represents the node as a string.
     * @return string The node's string representation.
     */
    public function strRepr(): string;
}

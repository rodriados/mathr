<?php
/**
 * The parsed expression.
 * @package Mathr\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator;

use Mathr\Parser\Node\NodeInterface;

/**
 * The expression queue built when parsing an expression.
 * @package Mathr\Evaluator
 */
class Expression
{
    /**
     * The expression node stack.
     * @var NodeInterface[] The stack node's that represent the expression.
     */
    private array $stack = [];

    /**
     * Pushes a node to the top of the expression stack.
     * @param NodeInterface $node The node to be pushed into the expression.
     */
    public function push(NodeInterface $node): void
    {
        array_push($this->stack, $node);
    }

    /**
     * Pops the node at the top of the expression stack.
     * @return NodeInterface The node popped from the expression.
     */
    public function pop(): NodeInterface
    {
        return array_shift($this->stack);
    }

    /**
     * Informs whether the expression is currently empty.
     * @return bool Is the expression empty?
     */
    public function isEmpty(): bool
    {
        return empty($this->stack);
    }
}

<?php
/**
 * Node for operators.
 * @package Mathr\Evaluator\Node
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator\Node;

use Mathr\Interperter\Token;
use Mathr\Contracts\Evaluator\NodeInterface;

/**
 * Represents a node in an expression node.
 * @package Mathr\Evaluator\Node
 */
class OperatorNode extends HierarchyNode
{
    /**
     * The operators' precedence.
     * Informs the order in which operators must be evaluated.
     */
    public const PRECEDENCE = [
        Token::OP_EQL =>  0,
        Token::OP_SUM =>  2,
        Token::OP_SUB =>  2,
        Token::OP_MUL =>  3,
        Token::OP_DIV =>  3,
        Token::OP_PWR =>  4,
        Token::OP_POS => 10,
        Token::OP_NEG => 10,
    ];

    /**
     * Represents the node as a string.
     * @return string The node's string representation.
     */
    public function strRepr(): string
    {
        return $this->getChildrenCount() == 1
            ? $this->strReprUnary()
            : $this->strReprBinary();
    }

    /**
     * Represents the unary operator as a string.
     * @return string The node's string representation.
     */
    private function strReprUnary(): string
    {
        [$child] = $this->getChildren();
        return sprintf('%s%s', $this->getData()[0], $this->strChild($child));
    }

    /**
     * Represents the binary operator as a string.
     * @return string The node's string representation.
     */
    private function strReprBinary(): string
    {
        [$left, $right] = $this->getChildren();
        return sprintf('%s %s %s', $this->strChild($left), $this->getData()[0], $this->strChild($right));
    }

    /**
     * Represents a child node as a string.
     * @param NodeInterface $child The child node to be represented as string.
     * @return string The child's string representation.
     */
    private function strChild(NodeInterface $child): string
    {
        if (!$child instanceof OperatorNode)
            return $child->strRepr();

        return self::PRECEDENCE[$child->getData()] <= self::PRECEDENCE[$this->getData()]
            ? sprintf('(%s)', $child->strRepr())
            : $child->strRepr();
    }
}

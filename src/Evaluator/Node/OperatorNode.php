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
use Mathr\Contracts\Evaluator\NodeException;
use Mathr\Contracts\Evaluator\MemoryInterface;
use Mathr\Contracts\Evaluator\EvaluationException;

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
     * Evaluates the node and produces a result.
     * @param MemoryInterface $memory The memory to lookup for bindings.
     * @return NodeInterface The produced resulting node.
     * @throws NodeException The produced result is invalid.
     * @throws EvaluationException The operator does is invalid.
     */
    public function evaluate(MemoryInterface $memory): NodeInterface
    {
        $hierarchy = $this->evaluateHierarchy($memory);

        return self::allOfNumbers($hierarchy)
            ? $this->evaluateOperator($hierarchy)
            : static::make($this->getData(), $hierarchy);
    }

    /**
     * Represents the node as a string.
     * @return string The node's string representation.
     */
    public function strRepr(): string
    {
        return $this->getHierarchyCount() == 1
            ? $this->strReprUnary()
            : $this->strReprBinary();
    }

    /**
     * Creates a new node from the node type and parameters.
     * @param string $data The operator to create the node for.
     * @param NodeInterface[] $args The operator's arguments.
     * @return static The created node.
     */
    public static function make(string $data, array $args): static
    {
        $token = new Token($data, type: Token::OPERATOR | (Token::OP_ASSOC[$data] ?? 0));
        return new static($token, $args);
    }

    /**
     * Evaluates the operator and produces a numeric result.
     * @param NodeInterface[] $params The operands to apply the operator to.
     * @return NodeInterface The produced resulting node.
     * @throws NodeException The produced result is invalid.
     * @throws EvaluationException The operator does is invalid.
     */
    private function evaluateOperator(array $params): NodeInterface
    {
        // TODO: Use bcmath and fallback to closures if not available.
        // TODO: Implement assignment operator.
        $result = match ($this->getData()) {
            Token::OP_SUM => floatval((string)$params[0]) + floatval((string)$params[1]),
            Token::OP_SUB => floatval((string)$params[0]) - floatval((string)$params[1]),
            Token::OP_MUL => floatval((string)$params[0]) * floatval((string)$params[1]),
            Token::OP_DIV => floatval((string)$params[0]) / floatval((string)$params[1]),
            Token::OP_PWR => pow(floatval((string)$params[0]), floatval((string)$params[1])),
            default       => throw new EvaluationException("Invalid operator")
        };

        return NumberNode::make($result);
    }

    /**
     * Represents the unary operator as a string.
     * @return string The node's string representation.
     */
    private function strReprUnary(): string
    {
        [$operand] = $this->getHierarchy();
        return sprintf('%s%s', $this->getData()[0], $this->strOperand($operand));
    }

    /**
     * Represents the binary operator as a string.
     * @return string The node's string representation.
     */
    private function strReprBinary(): string
    {
        [$l, $r] = $this->getHierarchy();
        return sprintf('%s %s %s', $this->strOperand($l), $this->getData()[0], $this->strOperand($r));
    }

    /**
     * Represents an operand node as a string.
     * @param NodeInterface $op The operand node to be represented as string.
     * @return string The operand's string representation.
     */
    private function strOperand(NodeInterface $op): string
    {
        if (!$op instanceof OperatorNode)
            return $op->strRepr();

        return self::PRECEDENCE[$op->getData()] <= self::PRECEDENCE[$this->getData()]
            ? sprintf('(%s)', $op->strRepr())
            : $op->strRepr();
    }
}

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
use Mathr\Evaluator\Assigner;
use Mathr\Contracts\Evaluator\NodeInterface;
use Mathr\Contracts\Evaluator\NodeException;
use Mathr\Contracts\Evaluator\MemoryInterface;
use Mathr\Contracts\Evaluator\MemoryException;
use Mathr\Contracts\Evaluator\AssignerException;
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
     * The list of operator functions.
     * @var callable[] The functions to use when evaluating each operator.
     */
    private static array $functors = [];

    /**
     * Evaluates the node and produces a result.
     * @param MemoryInterface $memory The memory to lookup for bindings.
     * @return NodeInterface The produced resulting node.
     * @throws NodeException The produced result is invalid.
     * @throws EvaluationException The operator is invalid.
     * @throws AssignerException An invalid binding was given.
     * @throws MemoryException The memory stack was overflown while evaluating contents.
     */
    public function evaluate(MemoryInterface $memory): NodeInterface
    {
        if (empty(self::$functors))
            self::$functors = self::loadOperatorFunctions();

        if ($this->getData() == Token::OP_EQL)
            return Assigner::assignToMemory($memory, ...$this->getHierarchy());

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
     * @throws EvaluationException The operator is invalid.
     */
    private function evaluateOperator(array $params): NodeInterface
    {
        $params = array_map(fn (NodeInterface $node) => $node->getData(), $params);
        $result = $this->getFunctor() (...$params);

        return NumberNode::make($result);
    }

    /**
     * Retrieves the functor bound the operator.
     * @return callable The functor bound to the current operator.
     * @throws EvaluationException The operator is invalid.
     */
    private function getFunctor(): callable
    {
        return self::$functors[$this->getData()]
            ?? throw EvaluationException::operatorIsInvalid($this);
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

    /**
     * Loads the functions to use when evaluating operators.
     * @return callable[] The list of operator functions.
     */
    private static function loadOperatorFunctions(): array
    {
        return !extension_loaded('bcmath')
            ? self::loadBuiltinOperators()
            : self::loadBCMathOperators();
    }

    /**
     * Loads built-in functions to use when evaluating operators.
     * @return callable[] The list of operator functions.
     */
    private static function loadBuiltinOperators(): array
    {
        return [
            Token::OP_SUM => fn ($x, $y) => $x + $y,
            Token::OP_SUB => fn ($x, $y) => $x - $y,
            Token::OP_MUL => fn ($x, $y) => $x * $y,
            Token::OP_DIV => fn ($x, $y) => $x / $y,
            Token::OP_PWR => fn ($x, $y) => $x ** $y,
            Token::OP_NEG => fn ($x) => -$x,
            Token::OP_POS => fn ($x) => +$x,
        ];
    }

    /**
     * Loads the BCMath extension functions to use when evaluating operators.
     * @return callable[] The list of operator functions.
     */
    private static function loadBCMathOperators(): array
    {
        return [
            Token::OP_SUM => fn ($x, $y) => bcadd($x, $y, 15),
            Token::OP_SUB => fn ($x, $y) => bcsub($x, $y, 15),
            Token::OP_MUL => fn ($x, $y) => bcmul($x, $y, 15),
            Token::OP_DIV => fn ($x, $y) => bcdiv($x, $y, 15),
            Token::OP_PWR => fn ($x, $y) => pow($x, $y),
            Token::OP_NEG => fn ($x) => bcmul($x, '-1', 15),
            Token::OP_POS => fn ($x) => $x,
        ];
    }
}

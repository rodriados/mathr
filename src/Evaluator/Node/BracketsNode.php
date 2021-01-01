<?php
/**
 * Node for brackets operator.
 * @package Mathr\Evaluator\Node
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator\Node;

use Mathr\Interperter\Token;
use Mathr\Contracts\Evaluator\NodeInterface;
use Mathr\Contracts\Evaluator\MemoryInterface;
use Mathr\Contracts\Evaluator\EvaluationException;

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
        return sprintf('[]@%d', $this->getHierarchyCount());
    }

    /**
     * Evaluates the node and produces a result.
     * @param MemoryInterface $memory The memory to lookup for bindings.
     * @return NodeInterface The produced resulting node.
     * @throws EvaluationException The brackets cannot be applied on expression.
     */
    public function evaluate(MemoryInterface $memory): NodeInterface
    {
        $hierarchy = $this->evaluateHierarchy($memory);
        $vector    = array_shift($hierarchy);

        if ($vector instanceof NumberNode)
            throw EvaluationException::cannotApplyBrackets($vector);

        return $vector instanceof VectorNode && self::allOfNumbers($hierarchy)
            ? $this->evaluateLookup($vector, $hierarchy)
            : static::make([$vector, ...$hierarchy]);
    }

    /**
     * Represents the node as a string.
     * @return string The node's string representation.
     */
    public function strRepr(): string
    {
        $hierarchy = $this->getHierarchy();
        $vector = array_shift($hierarchy);
        return sprintf("%s[%s]", $vector->strRepr(), static::strHierarchy($hierarchy));
    }

    /**
     * Creates a new node from the operator's children.
     * @param NodeInterface[] $args The operator's children.
     * @return static The created node.
     */
    public static function make(array $args): static
    {
        $token = new Token('[', type: Token::BRACKETS);
        return new static($token, $args);
    }

    /**
     * Retrieves a node from a vector.
     * @param VectorNode $vector The vector to retrieve a node from.
     * @param NodeInterface[] $lookup The indeces to lookup for a node.
     * @return NodeInterface The retrieved node.
     * @throws EvaluationException The brackets cannot be applied on expression.
     */
    private function evaluateLookup(VectorNode $vector, array $lookup): NodeInterface
    {
        $index  = array_shift($lookup);
        $index  = intval($index->getData());
        $result = $vector->getElement($index - 1);

        if (!empty($lookup) && !$result instanceof VectorNode)
            throw EvaluationException::cannotApplyBrackets($result);

        return !empty($lookup)
            ? $this->evaluateLookup($result, $lookup)
            : $result;
    }
}

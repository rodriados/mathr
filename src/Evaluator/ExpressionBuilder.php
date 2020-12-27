<?php
/**
 * The basic expression builder.
 * @package Mathr\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator;

use Mathr\Interperter\Token;
use Mathr\Evaluator\Node\NumberNode;
use Mathr\Evaluator\Node\VectorNode;
use Mathr\Evaluator\Node\OperatorNode;
use Mathr\Evaluator\Node\FunctionNode;
use Mathr\Evaluator\Node\BracketsNode;
use Mathr\Evaluator\Node\IdentifierNode;
use Mathr\Contracts\Evaluator\NodeInterface;
use Mathr\Contracts\Interperter\TokenInterface;
use Mathr\Contracts\Evaluator\ExpressionInterface;
use Mathr\Contracts\Evaluator\ExpressionBuilderInterface;
use Mathr\Contracts\Evaluator\ExpressionBuilderException;

/**
 * The expression builder used when parsing an expression.
 * @package Mathr\Evaluator
 */
class ExpressionBuilder implements ExpressionBuilderInterface
{
    /**
     * The expression node stack.
     * @var NodeInterface[] The stack of nodes pushed into the expression.
     */
    private array $stack = [];

    /**
     * Retrieves the built expression.
     * @return ExpressionInterface The built expression instance.
     */
    public function getExpression(): ExpressionInterface
    {
        return new Expression($this->stack[0]);
    }

    /**
     * Pushes a token into the expression builder.
     * @param TokenInterface $token The token to be pushed into the expression.
     * @param int|null $argc The number of arguments the token must take.
     * @throws ExpressionBuilderException Too many arguments requested from the stack.
     */
    public function push(TokenInterface $token, int $argc = null): void
    {
        match ($token->getType(Token::TYPE_MASK)) {
            Token::NUMBER      => $this->pushNumber($token),
            Token::IDENTIFIER  => $this->pushIdentifier($token),
            Token::OPERATOR    => $this->pushOperator($token),
            Token::FUNCTION    => $this->pushFunction($token, $argc),
            Token::BRACKETS    => $this->pushBrackets($token, $argc),
            Token::CURLYBRACES => $this->pushVector($token, $argc),
            default            => throw ExpressionBuilderException::tokenIsInvalid($token),
        };
    }

    /**
     * Pushes a number node onto the node stack.
     * @param TokenInterface $token The token to build the node from.
     * @return int The number of nodes currently on the stack.
     */
    private function pushNumber(TokenInterface $token): int
    {
        $node = new NumberNode($token);
        return array_push($this->stack, $node);
    }

    /**
     * Pushes an identifier node onto the node stack.
     * @param TokenInterface $token The token to build the node from.
     * @return int The number of nodes currently on the stack.
     */
    private function pushIdentifier(TokenInterface $token): int
    {
        $node = new IdentifierNode($token);
        return array_push($this->stack, $node);
    }

    /**
     * Pushes an operator node onto the node stack.
     * @param TokenInterface $token The token to build the node from.
     * @return int The number of nodes currently on the stack.
     * @throws ExpressionBuilderException Not enough arguments for operator on stack.
     */
    private function pushOperator(TokenInterface $token): int
    {
        $cardinality = $token->isOf(Token::UNARY) ? 1 : 2;
        $node = new OperatorNode($token, $this->requestNodes($cardinality));
        return array_push($this->stack, $node);
    }

    /**
     * Pushes a function node onto the node stack.
     * @param TokenInterface $token The token to build the node from.
     * @param int $argc The number of tokens passed as function arguments.
     * @return int The number of nodes currently on the stack.
     * @throws ExpressionBuilderException Not enough arguments for operator on stack.
     */
    private function pushFunction(TokenInterface $token, int $argc): int
    {
        $node = new FunctionNode($token, $this->requestNodes($argc));
        return array_push($this->stack, $node);
    }

    /**
     * Pushes a brackets node onto the node stack.
     * @param TokenInterface $token The token to build the node from.
     * @param int $argc The number of tokens passed as function arguments.
     * @return int The number of nodes currently on the stack.
     * @throws ExpressionBuilderException Not enough arguments for operator on stack.
     */
    private function pushBrackets(TokenInterface $token, int $argc): int
    {
        $node = new BracketsNode($token, $this->requestNodes($argc + 1));
        return array_push($this->stack, $node);
    }

    /**
     * Pushes a vector node onto the node stack.
     * @param TokenInterface $token The token to build the node from.
     * @param int $argc The number of tokens passed as function arguments.
     * @return int The number of nodes currently on the stack.
     * @throws ExpressionBuilderException Not enough arguments for operator on stack.
     */
    private function pushVector(TokenInterface $token, int $argc): int
    {
        $node = new VectorNode($token, $this->requestNodes($argc));
        return array_push($this->stack, $node);
    }

    /**
     * Pops from the node stack the requested amount of nodes.
     * @param int $count The amount of nodes to pop from the stack.
     * @return NodeInterface[] The nodes popped from the stack.
     * @throws ExpressionBuilderException Too many arguments were requested from stack.
     */
    private function requestNodes(int $count = 1): array
    {
        if (count($this->stack) < $count)
            throw ExpressionBuilderException::argumentsAreNotEnough();

        for ($i = 0; $i < $count; ++$i)
            $result[] = array_pop($this->stack);

        return array_reverse($result ?? []);
    }
}

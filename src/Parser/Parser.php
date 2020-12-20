<?php
/**
 * Mathematical expression parser.
 * @package Mathr\Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Parser;

use SplStack as Stack;
use Mathr\Parser\Node\Node;
use Mathr\Parser\Node\NullNode;
use Mathr\Evaluator\Expression;
use Mathr\Parser\Node\PairNode;
use Mathr\Parser\Node\NumberNode;
use Mathr\Parser\Node\VectorNode;
use Mathr\Parser\Node\FunctionNode;
use Mathr\Parser\Node\VariableNode;
use Mathr\Parser\Node\OperatorNode;
use Mathr\Parser\Node\BracketsNode;
use Mathr\Parser\Node\NodeInterface;
use Mathr\Parser\Node\ParenthesisNode;

/**
 * Parses a mathematical expression.
 * @package Mathr\Parser
 */
class Parser
{
    /**
     * All operators are stacked after being sent to output.
     * @var Stack The operator stack.
     */
    private Stack $stack;

    /**
     * The expression parsed from the input.
     * @var Expression The parsed expression.
     */
    private Expression $output;

    /**
     * Indicates whether the next consumed token should be an operator.
     * @var bool Does the current parsing state expect an operator?
     */
    private bool $expectOperator = false;

    /**
     * Parses the input and produces an expression tree.
     * @param string $input The input to parse.
     * @param TokenizerInterface|null $tokenizer The tokenizer to use when parsing.
     * @return Expression The expression tree produced by the parsing.
     * @throws ParserException An unexpected error while parsing.
     */
    public static function parse(string $input, ?TokenizerInterface $tokenizer = null): Expression
    {
        $parser = new static($input, $tokenizer ?? new Tokenizer);
        return $parser->build();
    }

    /**
     * Parser constructor.
     * @param string $input The input to parse.
     * @param TokenizerInterface $tokenizer The tokenizer to parse expression with.
     */
    protected function __construct(
        private string $input,
        private TokenizerInterface $tokenizer
    ) {
        $this->stack  = new Stack;
        $this->output = new Expression();
        $this->tokenizer->tokenize($this->input);
    }

    /**
     * Builds an expression from tokens.
     * @return Expression The resulting expression tree.
     * @throws ParserException An unexpected error while parsing.
     */
    protected function build(): Expression
    {
        foreach ($this->tokenizer as $token)
            $this->consume($token);

        return $this->output;
    }

    /**
     * Consumes a token and builds up the expression.
     * @param Token $token The token to be consumed.
     * @return NodeInterface The node that has just been created.
     * @throws ParserException An unexpected token while parsing.
     */
    private function consume(Token $token): NodeInterface
    {
        return match ($token->getType(Token::TYPE_MASK)) {
            Token::NUMBER      => $this->consumeNumber($token),
            Token::IDENTIFIER  => $this->consumeIdentifier($token),
            Token::OPERATOR    => $this->consumeOperator($token),
            Token::PARENTHESIS => $this->consumeParenthesis($token),
            Token::BRACKETS    => $this->consumeBrackets($token),
            Token::CURLY       => $this->consumeCurly($token),
            Token::COMMA       => $this->consumeComma($token),
            Token::EOS         => $this->consumeEOS($token),
            default            => throw ParserException::unexpectedToken($token),
        };
    }

    /**
     * Consumes a number token.
     * @param Token $token The token to be consumed.
     * @return NumberNode The created node.
     * @throws ParserException An unexpected number while parsing.
     */
    private function consumeNumber(Token $token): NumberNode
    {
        if ($this->expectOperator)
            throw ParserException::unexpectedToken($token);

        $this->functionIncrement();

        $current = new NumberNode($token);

        $this->expectOperator = true;
        $this->output->push($current);

        return $current;
    }

    /**
     * Consumes an identifier token.
     * @param Token $token The token to be consumed.
     * @return NodeInterface The created node.
     * @throws ParserException An unexpected token when parsing.
     */
    private function consumeIdentifier(Token $token): NodeInterface
    {
        if ($this->expectOperator)
            $this->consumeImplicitOperator($token);

        $this->functionIncrement();

        return $token->getData()[-1] == '('
            ? $this->consumeFunction($token)
            : $this->consumeVariable($token);
    }

    /**
     * Consumes a function token.
     * @param Token $token The token to be consumed.
     * @return FunctionNode The created node.
     */
    private function consumeFunction(Token $token): FunctionNode
    {
        $current = new FunctionNode($token);

        $this->expectOperator = false;
        $this->stack->push($current);

        return $current;
    }

    /**
     * Consumes a variable token.
     * @param Token $token The token to be consumed.
     * @return VariableNode The created node.
     */
    private function consumeVariable(Token $token): VariableNode
    {
        $current = new VariableNode($token);

        $this->expectOperator = true;
        $this->output->push($current);

        return $current;
    }

    /**
     * Consumes an operator token.
     * @param Token $token The token to be consumed.
     * @return OperatorNode The created node.
     * @throws ParserException An unexpected token when parsing.
     */
    public function consumeOperator(Token $token): OperatorNode
    {
        if (!$this->expectOperator)
            $token = $this->tryMakeUnary($token);

        $current = new OperatorNode($token);

        if ($current->getAssoc() == Token::RIGHT)
            $this->sendOperatorsToOutput($current);

        $this->expectOperator = false;
        $this->stack->push($current);

        return $current;
    }

    /**
     * Consumes an implicit multiplication operator.
     * @param Token $token The token that trigger implicity.
     * @return OperatorNode The created node.
     * @throws ParserException An unexpected token when parsing.
     */
    private function consumeImplicitOperator(Token $token): OperatorNode
    {
        return $this->consumeOperator(
            new Token(OperatorNode::MUL, $token->getPosition(), Token::OPERATOR | Token::RIGHT)
        );
    }

    /**
     * Consumes a parenthesis token.
     * @param Token $token The token to be consumed.
     * @return PairNode The created node.
     * @throws ParserException An unexpected or mismatched token when parsing.
     */
    private function consumeParenthesis(Token $token): PairNode
    {
        $current = new ParenthesisNode($token);

        return $token->is(ParenthesisNode::getOpeningPair())
            ? $this->consumeOpenPair($current)
            : $this->consumeClosePair($current);
    }

    /**
     * Consumes a brackets token.
     * @param Token $token The token to be consumed.
     * @return PairNode The created node.
     * @throws ParserException An unexpected or unmatched token when parsing.
     */
    private function consumeBrackets(Token $token): PairNode
    {
        $current = new BracketsNode($token);

        if (!$this->expectOperator)
            throw ParserException::unexpectedToken($token);

        return $token->is(BracketsNode::getOpeningPair())
            ? $this->consumeOpenPair($current, allowImplicit: false)
            : $this->consumeClosePair($current);
    }

    /**
     * Consumes a curly-brackets token.
     * @param Token $token The token to be consumed.
     * @return PairNode The created node.
     * @throws ParserException An unexpected or unmatched token when parsing.
     */
    private function consumeCurly(Token $token): PairNode
    {
        $current = new VectorNode($token);

        return $token->is(VectorNode::getOpeningPair())
            ? $this->consumeOpenPair($current)
            : $this->consumeClosePair($current);
    }

    /**
     * Consumes an opening pair token.
     * @param PairNode $current The opening pair's node.
     * @param bool $allowImplicit Is the insertion of an implicit operator allowed?
     * @return PairNode The opening pair's node.
     * @throws ParserException An unexpected token when parsing.
     */
    private function consumeOpenPair(PairNode $current, bool $allowImplicit = true): PairNode
    {
        if ($allowImplicit && $this->expectOperator)
            $this->consumeImplicitOperator($current->getToken());

        if ($current instanceof VectorNode)
            $this->functionIncrement();

        $this->expectOperator = false;
        $this->stack->push($current);

        return $current;
    }

    /**
     * Consumes a closing pair token.
     * @param PairNode $current The closing pair's node.
     * @return PairNode The closing pair's node.
     * @throws ParserException The token is mismatched.
     */
    private function consumeClosePair(PairNode $current): PairNode
    {
        if (!$this->expectOperator)
            throw ParserException::unexpectedToken($current->getToken());

        $this->sendOperatorsToOutput();

        if ($this->stack->isEmpty())
            throw ParserException::mismatchedToken($current->getToken());

        $popped = $this->stack->pop();

        if (!$popped instanceof PairNode)
            throw ParserException::mismatchedToken($current->getToken());

        $popped->close($current);

        if ($popped instanceof FunctionNode)
            $this->output->push($popped);

        return $current;
    }

    /**
     * Consumes a comma token.
     * @param Token $token The token to be consumed.
     * @return NullNode The created node.
     * @throws ParserException An unexpected token while parsing.
     */
    private function consumeComma(Token $token): NullNode
    {
        if (!$this->expectOperator)
            throw ParserException::unexpectedToken($token);

        $this->sendOperatorsToOutput();

        if ($this->stack->isEmpty() || !$this->stack->top() instanceof FunctionNode)
            throw ParserException::unexpectedToken($token);

        $this->expectOperator = false;

        return new NullNode($token);
    }

    /**
     * Consumes an End-Of-String token.
     * @param Token $token The token to be consumed.
     * @return NullNode The created node.
     * @throws ParserException An unexpected token while parsing.
     */
    private function consumeEOS(Token $token): NullNode
    {
        $this->sendOperatorsToOutput();

        if ($this->stack->isEmpty())
            return new NullNode($token);

        $top = $this->stack->top();

        if ($top instanceof PairNode)
            throw ParserException::mismatchedToken($top->getToken());

        if ($top instanceof Node)
            throw ParserException::unexpectedToken($top->getToken());

        throw ParserException::invalidExpression();
    }

    /**
     * If the parsing is a function context, increment its argument count.
     * @return int|null The function's current argument count.
     */
    private function functionIncrement(): ?int
    {
        if ($this->stack->isEmpty())
            return null;

        $stacked = $this->stack->top();

        if (!$stacked instanceof FunctionNode)
            return null;

        return $stacked->argIncrement();
    }

    /**
     * Send all operators with higher precedence on stack to the output.
     * @param OperatorNode|null $current The operator being currently parsed.
     */
    private function sendOperatorsToOutput(?OperatorNode $current = null): void
    {
        while (!$this->stack->isEmpty()) {
            $top = $this->stack->top();

            if (!$top instanceof OperatorNode)
                return;

            if ($top->getPrecedence() < $current?->getPrecedence())
                return;

            $top = $this->stack->pop();
            $this->output->push($top);
        }
    }

    /**
     * Tries to transform the token into its unary counterpart.
     * @param Token $token The token to be transformed.
     * @return Token The unary token created.
     * @throws ParserException An unexpected token when parsing.
     */
    private function tryMakeUnary(Token $token): Token
    {
        if (!in_array($token->getData(), Token::MAYBE_UNARY))
            throw ParserException::unexpectedToken($token);

        $this->functionIncrement();

        return Token::makeUnary($token);
    }
}

<?php
/**
 * Stateful mathematical expression parser.
 * @package Mathr\Interperter\Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Interperter\Parser;

use SplStack as Stack;
use Mathr\Interperter\Token;
use Mathr\Interperter\Parser;
use Mathr\Evaluator\ExpressionBuilder;
use Mathr\Contracts\Interperter\TokenInterface;
use Mathr\Contracts\Interperter\ParserException;
use Mathr\Contracts\Evaluator\ExpressionBuilderInterface;
use Mathr\Contracts\Interperter\TokenizerInterface;

/**
 * Parses a mathematical expression using states.
 * @package Mathr\Interperter\Parser
 */
class StatefulParser extends Parser
{
    /**
     * The list of token compositions.
     * @var array The list of compositions to be applied by this parser.
     */
    private const COMPOSITION = [
        Token::FUNCTION | Token::OPEN => [ Token::IDENTIFIER, Token::PARENTHESIS | Token::OPEN ],
    ];

    /**
     * Parses the given string into an expression instance.
     * @param TokenizerInterface $tokenizer The tokenizer to extract tokens with.
     * @param string $expression The expression to be parsed.
     * @return ExpressionBuilderInterface The expression builder instance.
     * @throws ParserException An unexpected token while parsing.
     */
    protected static function parseExpression(TokenizerInterface $tokenizer, string $expression)
        : ExpressionBuilderInterface
    {
        return static::produceExpression(
            static::stateFactory(),
            static::composeTokens($tokenizer->runTokenizer($expression)),
        );
    }

    /**
     * Creates a new state for the parser.
     * @return object The parser's new empty state.
     */
    protected static function stateFactory(): object
    {
        return (object) [
            'stack'          => new Stack,
            'expression'     => new ExpressionBuilder,
            'expectOperator' => false,
        ];
    }

    /**
     * Produces an expression from a list of tokens.
     * @param object $state A new parsing state.
     * @param TokenInterface[] $tokens The list of tokens extracted from input.
     * @return ExpressionBuilderInterface The expression builder instance.
     * @throws ParserException An unexpected token while parsing.
     */
    protected static function produceExpression(object $state, array $tokens): ExpressionBuilderInterface
    {
        foreach ($tokens as $token)
            $state = static::consumeToken($state, $token);

        return $state->expression;
    }

    /**
     * Compose simple tokens into more complex constructs.
     * @param TokenInterface[] $tokens The raw list of tokens.
     * @return TokenInterface[] The list of tokens after composition.
     */
    protected static function composeTokens(array $tokens): array
    {
        foreach (self::COMPOSITION as $product => $formula)
            $tokens = self::applyComposition($tokens, $formula, $product);

        return $tokens;
    }

    /**
     * Applies the given composition formula to the list of tokens.
     * @param TokenInterface[] $tokens The original list of tokens.
     * @param array $formula The composition formula to be applied.
     * @param int $product The type of token to build for the given composition.
     * @return TokenInterface[] The transformed list of tokens.
     */
    private static function applyComposition(array $tokens, array $formula, int $product): array
    {
        for ($offset = 0; $offset < count($tokens); ) {
            $slice = array_slice($tokens, $offset, count($formula));
            $match = array_map(fn ($token, $required) => $token?->isOf($required), $slice, $formula);

            if (!in_array(false, $match)) {
                $composed[] = self::joinTokens($slice, $product);
                $offset += count($formula);
            } else {
                $composed[] = $tokens[$offset];
                $offset += 1;
            }
        }

        return $composed ?? $tokens;
    }

    /**
     * Builds a new token by joining together a slice of tokens.
     * @param TokenInterface[] $slice The slice to tokens to be joined together.
     * @param int $product The type of final token produced.
     * @return TokenInterface The produced token instance.
     */
    private static function joinTokens(array $slice, int $product): TokenInterface
    {
        return new Token(
            data: array_reduce($slice, fn ($carry, $token) => $carry . $token->getData()),
            position: $slice[0]->getPosition(),
            type: $product
        );
    }

    /**
     * Consumes a token and builds up the expression.
     * @param object $state The current parsing state.
     * @param TokenInterface $token The token to be consumed.
     * @return object The updated parsing state after the consumed token.
     * @throws ParserException An unexpected token while parsing.
     */
    protected static function consumeToken(object $state, TokenInterface $token): object
    {
        return match ($token->getType(Token::TYPE_MASK)) {
            Token::NUMBER      => self::consumeNumber($state, $token),
            Token::IDENTIFIER  => self::consumeIdentifier($state, $token),
            Token::OPERATOR    => self::consumeOperator($state, $token),
            Token::FUNCTION    => self::consumeParenthesis($state, $token),
            Token::PARENTHESIS => self::consumeParenthesis($state, $token),
            Token::COMMA       => self::consumeComma($state, $token),
            Token::EOS         => self::consumeEOS($state, $token),
            default            => throw ParserException::tokenIsUnexpected($token),
        };
    }

    /**
     * Consumes a number token.
     * @param object $state The current parsing state.
     * @param TokenInterface $token The token to be consumed.
     * @return object The updated parsing state after the consumed token.
     * @throws ParserException An unexpected number while parsing.
     */
    private static function consumeNumber(object $state, TokenInterface $token): object
    {
        if ($state->expectOperator)
            throw ParserException::tokenIsUnexpected($token);

        $state = self::functionIncrement($state);
        $state->expression->push($token);
        $state->expectOperator = true;

        return $state;
    }

    /**
     * Consumes an identifier token.
     * @param object $state The current parsing state.
     * @param TokenInterface $token The token to be consumed.
     * @return object The updated parsing state after the consumed token.
     * @throws ParserException An unexpected token when parsing.
     */
    private static function consumeIdentifier(object $state, TokenInterface $token): object
    {
        if ($state->expectOperator)
            $state = self::consumeImplicitOperator($state, $token);

        $state = self::functionIncrement($state);

        $state->expression->push($token);
        $state->expectOperator = true;

        return $state;
    }

    /**
     * Consumes an operator token.
     * @param object $state The current parsing state.
     * @param TokenInterface $token The token to be consumed.
     * @return object The updated parsing state after the consumed token.
     * @throws ParserException An unexpected token when parsing.
     */
    public static function consumeOperator(object $state, TokenInterface $token): object
    {
        if (!$state->expectOperator)
            [$state, $token] = self::makeUnary($state, $token);

        if ($token->getType(Token::ASSOC_MASK) == Token::RIGHT)
            $state = self::operatorsToOutput($state, $token);

        $state->stack->push([$token]);
        $state->expectOperator = false;

        return $state;
    }

    /**
     * Consumes an implicit multiplication operator.
     * @param object $state The current parsing state.
     * @param TokenInterface $token The token that triggered implicity.
     * @return object The updated parsing state after the consumed token.
     * @throws ParserException An unexpected token when parsing.
     */
    private static function consumeImplicitOperator(object $state, TokenInterface $token): object
    {
        $implicit = new Token(Token::OP_MUL, $token->getPosition(), Token::OPERATOR | Token::RIGHT);
        return self::consumeOperator($state, $implicit);
    }

    /**
     * Consumes a parenthesis token.
     * @param object $state The current parsing state.
     * @param TokenInterface $token The token to be consumed.
     * @return object The updated parsing state after the consumed token.
     * @throws ParserException An unexpected or mismatched token when parsing.
     */
    private static function consumeParenthesis(object $state, TokenInterface $token): object
    {
        // If the parenthesis we are closing corresponds to a function we have just
        // opened and it hasn't taken any arguments, then it is an atomic function,
        // therefore it is a valid construct as long as an operator comes next.
        if (!$state->expectOperator && $token->isOf(Token::CLOSE))
            if($state->stack->top()[0]?->isOf(Token::FUNCTION))
                if($state->stack->top()[1] == 0)
                    $state->expectOperator = true;

        return $token->isOf(Token::OPEN)
            ? self::consumePairOpen($state, $token)
            : self::consumePairClose($state, $token);
    }

    /**
     * Consumes an opening pair token.
     * @param object $state The current parsing state.
     * @param TokenInterface $token The token to be consumed.
     * @return object The updated parsing state after the consumed token.
     * @throws ParserException An unexpected token when parsing.
     */
    private static function consumePairOpen(object $state, TokenInterface $token): object
    {
        if ($state->expectOperator && $token->getType(Token::VALUED | Token::PARENTHESIS))
            $state = self::consumeImplicitOperator($state, $token);

        if ($token->getType(Token::VALUED | Token::PARENTHESIS))
            $state = self::functionIncrement($state);

        $state->stack->push([$token, 0]);
        $state->expectOperator = false;

        return $state;
    }

    /**
     * Consumes a closing pair token.
     * @param object $state The current parsing state.
     * @param TokenInterface $token The token to be consumed.
     * @return object The updated parsing state after the consumed token.
     * @throws ParserException An unexpected token when parsing.
     */
    private static function consumePairClose(object $state, TokenInterface $token): object
    {
        if (!$state->expectOperator)
            throw ParserException::tokenIsUnexpected($token);

        $state = self::operatorsToOutput($state);

        if ($state->stack->isEmpty())
            throw ParserException::tokenIsMismatched($token);

        $last = $state->stack->pop();
        $type = $token->getType(Token::TYPE_MASK);

        if (!$last[0]->isOf($type | Token::OPEN))
            throw ParserException::tokenIsMismatched($token);

        if ($last[0]->getType(Token::VALUED))
            $state->expression->push(...$last);

        return $state;
    }

    /**
     * Consumes a comma token.
     * @param object $state The current parsing state.
     * @param TokenInterface $token The token to be consumed.
     * @return object The updated parsing state after the consumed token.
     * @throws ParserException An unexpected token when parsing.
     */
    private static function consumeComma(object $state, TokenInterface $token): object
    {
        if (!$state->expectOperator)
            throw ParserException::tokenIsUnexpected($token);

        $state = self::operatorsToOutput($state);
        $state->expectOperator = false;

        if ($state->stack->isEmpty() || !$state->stack->top()[0]->getType(Token::ARGUMENTED))
            throw ParserException::tokenIsUnexpected($token);

        return $state;
    }

    /**
     * Consumes an End-Of-String token.
     * @param object $state The current parsing state.
     * @param TokenInterface $token The token to be consumed.
     * @return object The updated parsing state after the consumed token.
     * @throws ParserException An unexpected token when parsing.
     */
    private static function consumeEOS(object $state, TokenInterface $token): object
    {
        if ($state->expectOperator)
            $state = self::operatorsToOutput($state);

        if ($state->stack->isEmpty())
            return $state;

        $last = $state->stack->pop();

        if ($last[0]->getType(Token::PAIRED))
            throw ParserException::tokenIsMismatched($last[0]);

        throw ParserException::tokenIsUnexpected($last[0]);
    }

    /**
     * If the parsing is a function context, increment its argument count.
     * @param object $state The current parsing state.
     * @return object The updated parsing state after function increment.
     */
    private static function functionIncrement(object $state): object
    {
        if (!$state->stack->isEmpty()) {
            if ($state->stack->top()[0]->isOf(Token::FUNCTION)) {
                [$function, $count] = $state->stack->pop();
                $state->stack->push([$function, ++$count]);
            }
        }

        return $state;
    }

    /**
     * Send all operators with higher precedence on stack to the output.
     * @param object $state The current parsing state.
     * @param TokenInterface|null $token The token that triggered the operation.
     * @return object The updated parsing state after the consumed token.
     */
    private static function operatorsToOutput(object $state, TokenInterface $token = null): object
    {
        while (!$state->stack->isEmpty()) {
            $last = $state->stack->top();

            if (!$last[0]->isOf(Token::OPERATOR))
                break;

            if ($token && self::comparePrecedence($last[0], $token) < 0)
                break;

            $state->stack->pop();
            $state->expression->push(...$last);
        }

        return $state;
    }

    /**
     * Tries to transform the token into its unary counterpart.
     * @param object $state The current parsing state.
     * @param TokenInterface $token The token to be transformed.
     * @return array The parsing state and the unary token created.
     * @throws ParserException An unexpected token when parsing.
     */
    private static function makeUnary(object $state, TokenInterface $token): array
    {
        $unary = Token::makeUnary($token);
        $state = self::functionIncrement($state);

        if (is_null($unary))
            throw ParserException::tokenIsUnexpected($token);

        return [$state, $unary];
    }
}

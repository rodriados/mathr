<?php
/**
 * An abstract parser implementation.
 * @package Mathr\Interperter
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Interperter;

use Mathr\Contracts\Interperter\TokenInterface;
use Mathr\Contracts\Interperter\ParserException;
use Mathr\Contracts\Interperter\ParserInterface;
use Mathr\Contracts\Evaluator\ExpressionBuilderInterface;
use Mathr\Contracts\Interperter\TokenizerInterface;

/**
 * The abstract base for the project's internal parsers.
 * @package Mathr\Interperter
 */
abstract class Parser implements ParserInterface
{
    /**
     * The operators' precedence.
     * Informs the order in which operators must be evaluated.
     */
    protected const PRECEDENCE = [
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
     * Parser constructor.
     * @param TokenizerInterface $tokenizer The tokenizer to use when parsing.
     */
    public function __construct(
        private TokenizerInterface $tokenizer
    ) {}

    /**
     * Parses the given expression.
     * @param string $expression The expression to be parsed.
     * @return ExpressionBuilderInterface The parser builder instance.
     * @throws ParserException An unexpected token while parsing.
     */
    final public function runParser(string $expression): ExpressionBuilderInterface
    {
        return static::parseExpression($this->tokenizer, $expression);
    }

    /**
     * Evaluates the order of precedence between two operators.
     * @param TokenInterface $a The first operator to be compared.
     * @param TokenInterface $b The second operator to be compared.
     * @return int|null The comparison's result.
     */
    protected static function comparePrecedence(TokenInterface $a, TokenInterface $b): ?int
    {
        return isset(self::PRECEDENCE[$a->getData()], self::PRECEDENCE[$b->getData()])
            ? self::PRECEDENCE[$a->getData()] <=> self::PRECEDENCE[$b->getData()]
            : null;
    }

    /**
     * Parses the given string into an expression instance.
     * @param TokenizerInterface $tokenizer The tokenizer to extract tokens with.
     * @param string $expression The expression to be parsed.
     * @return ExpressionBuilderInterface The parsed expression instance.
     * @throws ParserException An unexpected token while parsing.
     */
    protected abstract static function parseExpression(TokenizerInterface $tokenizer, string $expression)
        : ExpressionBuilderInterface;
}

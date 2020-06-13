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

/**
 * Parses a mathematical expression.
 * @package Mathr\Parser
 */
class Parser
{
    /**
     * All operators are stacked after being parsed.
     * @var Stack The operator stack.
     */
    private Stack $stack;

    /**
     * Parses the expression and produces an expression tree.
     * @param string $expression The expression to parse.
     * @param TokenizerInterface|null $tokenizer The tokenizer to use when parsing.
     * @return ExpressionTree The expression tree produced by the parsing.
     */
    public static function parse(string $expression, ?TokenizerInterface $tokenizer = null): ExpressionTree
    {
        $parser = new static($expression, $tokenizer ?? new Tokenizer);
        return $parser->build();
    }

    /**
     * Parser constructor.
     * @param string $expression The expression to parse.
     * @param TokenizerInterface $tokenizer The tokenizer to parse expression with.
     */
    protected function __construct(
        private string $expression,
        private TokenizerInterface $tokenizer
    ) {
        $this->stack = new Stack;
        $this->tokenizer->tokenize($this->expression);
    }
}

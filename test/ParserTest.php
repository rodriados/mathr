<?php
/**
 * Parser unit tests.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */

use Mathr\Parser\Parser;
use Mathr\Evaluator\Expression;
use PHPUnit\Framework\TestCase;

/**
 * The Mathr\Parser test class.
 */
final class ParserTest extends TestCase
{
    /**
     * Checks whether an expression is returned by parsing.
     */
    public function testIfParseReturnsExpression()
    {
        $expression = "fib(x) = fib(x - 1) + fib(x - 2)";
        $this->assertInstanceOf(Expression::class, Parser::parse($expression));
    }
}

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
use Mathr\Parser\ParserException;
use Mathr\Parser\Node\NumberNode;
use Mathr\Parser\Node\VectorNode;
use Mathr\Parser\Node\BracketsNode;
use Mathr\Parser\Node\FunctionNode;
use Mathr\Parser\Node\OperatorNode;
use Mathr\Parser\Node\VariableNode;
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
        $this->assertInstanceOf(Expression::class, Parser::parse('2 + 3'));
    }

    /**
     * Tests whether a simple expression can be parsed.
     */
    public function testIfParsesSimpleExpression()
    {
        $expression = "x + 3(2x + 4y)^2";

        $expected = [
            [ VariableNode::class, '$x' ],
            [   NumberNode::class,  '3' ],
            [   NumberNode::class,  '2' ],
            [ VariableNode::class, '$x' ],
            [ OperatorNode::class,  '*' ],
            [   NumberNode::class,  '4' ],
            [ VariableNode::class, '$y' ],
            [ OperatorNode::class,  '*' ],
            [ OperatorNode::class,  '+' ],
            [   NumberNode::class,  '2' ],
            [ OperatorNode::class,  '^' ],
            [ OperatorNode::class,  '*' ],
            [ OperatorNode::class,  '+' ],
        ];

        $parsed = Parser::parse($expression);

        foreach ($expected as [ $type, $value ]) {
            $current = $parsed->pop();
            $this->assertInstanceOf($type, $current);
            $this->assertEquals($value, (string) $current);
        }

        $this->assertTrue($parsed->isEmpty());
    }

    /**
     * Tests whether functions are correctly parsed.
     */
    public function testIfParsesFunctionExpression()
    {
        $expression = "f(x, y) = g(x + y) + -(h(x - 1)^2 - i(-x, -y))";

        $expected = [
            [ VariableNode::class,  '$x' ],
            [ VariableNode::class,  '$y' ],
            [ FunctionNode::class, 'f@2' ],
            [ VariableNode::class,  '$x' ],
            [ VariableNode::class,  '$y' ],
            [ OperatorNode::class,   '+' ],
            [ FunctionNode::class, 'g@1' ],
            [ VariableNode::class,  '$x' ],
            [   NumberNode::class,   '1' ],
            [ OperatorNode::class,   '-' ],
            [ FunctionNode::class, 'h@1' ],
            [   NumberNode::class,   '2' ],
            [ OperatorNode::class,   '^' ],
            [ VariableNode::class,  '$x' ],
            [ OperatorNode::class,  'U-' ],
            [ VariableNode::class,  '$y' ],
            [ OperatorNode::class,  'U-' ],
            [ FunctionNode::class, 'i@2' ],
            [ OperatorNode::class,   '-' ],
            [ OperatorNode::class,  'U-' ],
            [ OperatorNode::class,   '+' ],
            [ OperatorNode::class,   '=' ],
        ];

        $parsed = Parser::parse($expression);

        foreach ($expected as [ $type, $value ]) {
            $current = $parsed->pop();
            $this->assertInstanceOf($type, $current);
            $this->assertEquals($value, (string) $current);
        }

        $this->assertTrue($parsed->isEmpty());
    }

    /**
     * Tests whether expressions with vectors can be parsed.
     */
    public function testIfParsesVectorExpression()
    {
        $expression = "{{1, 2, 3 + 1}, 3 {4 ^ 2, 5, 6}, -{7, f(8, x), 9}}[1, 2 * 3]";

        $expected = [
            [   NumberNode::class,    '1' ],
            [   NumberNode::class,    '2' ],
            [   NumberNode::class,    '3' ],
            [   NumberNode::class,    '1' ],
            [ OperatorNode::class,    '+' ],
            [   VectorNode::class, '{}@3' ],
            [   NumberNode::class,    '3' ],
            [   NumberNode::class,    '4' ],
            [   NumberNode::class,    '2' ],
            [ OperatorNode::class,    '^' ],
            [   NumberNode::class,    '5' ],
            [   NumberNode::class,    '6' ],
            [   VectorNode::class, '{}@3' ],
            [ OperatorNode::class,    '*' ],
            [   NumberNode::class,    '7' ],
            [   NumberNode::class,    '8' ],
            [ VariableNode::class,   '$x' ],
            [ FunctionNode::class,  'f@2' ],
            [   NumberNode::class,    '9' ],
            [   VectorNode::class, '{}@3' ],
            [ OperatorNode::class,   'U-' ],
            [   VectorNode::class, '{}@3' ],
            [   NumberNode::class,    '1' ],
            [   NumberNode::class,    '2' ],
            [   NumberNode::class,    '3' ],
            [ OperatorNode::class,    '*' ],
            [ BracketsNode::class, '[]@2' ],
        ];

        $parsed = Parser::parse($expression);

        foreach ($expected as [ $type, $value ]) {
            $current = $parsed->pop();
            $this->assertInstanceOf($type, $current);
            $this->assertEquals($value, (string) $current);
        }

        $this->assertTrue($parsed->isEmpty());
    }

    /**
     * Tests whether mismatched pair nodes can be detected.
     * @dataProvider mismatchedExpressions
     */
    public function testIfDetectsMismatches(string $expression)
    {
        $this->expectException(ParserException::class);
        Parser::parse($expression);
    }

    /**
     * Provides mismatched expressions for testing.
     * @return string[][] The mismatched expresions.
     */
    public function mismatchedExpressions(): array
    {
        return [
            [ '(((((1))))' ],
            [ '{{1,2,3})'  ],
            [ '{1,2,3}[1'  ],
            [ '((1 + 2)))' ],
        ];
    }
}

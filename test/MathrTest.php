<?php
/**
 * Mathr integration tests.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */

use Mathr\Mathr;
use Mathr\Evaluator\Node\NumberNode;
use Mathr\Contracts\Evaluator\NodeInterface;
use PHPUnit\Framework\TestCase;
use Mathr\Contracts\Evaluator\AssignerException;

/**
 * The project's complete integration test class.
 * @package Mathr
 */
final class MathrTest extends TestCase
{
    /**
     * The project's instance.
     * @var Mathr The project's instance.
     */
    private Mathr $mathr;

    /**
     * Sets the tests' environment up.
     * @since 3.0
     */
    protected function setUp(): void
    {
        $this->mathr = new Mathr();
    }

    /**
     * Checks whether the project has been successfully instantiated.
     * @since 3.0
     */
    public function testIfCanBeInstantiated()
    {
        $this->assertInstanceOf(Mathr::class, $this->mathr);
    }

    /**
     * Tests whether a simple expression can be evaluated.
     * @param string $expression The expression to be tested.
     * @param string $expected The expected test result.
     * @dataProvider provideSimpleExpressions
     * @since 3.0
     */
    public function testIfParsesSimpleExpression(string $expression, string $expected)
    {
        $evaluated = $this->mathr->evaluate($expression);

        $this->assertInstanceOf(NodeInterface::class, $evaluated);
        $this->assertInstanceOf(NumberNode::class, $evaluated);
        $this->assertEquals($expected, $evaluated->strRepr());
    }

    /**
     * Provides simple valid expressions for testing.
     * @return string[][] The expressions and their expected result.
     */
    public function provideSimpleExpressions(): array
    {
        return [
            [  "2.1 + 3.3",    "5.4" ],
            [  "7.6 - 4.9",    "2.7" ],
            [  "1.4 * 4.7",   "6.58" ],
            [  "8.7 / 2.9",      "3" ],
            [  "1.1 ^ 2.0",   "1.21" ],
            [  "2 + 3 * 4",     "14" ],
            [  "(2+3) * 4",     "20" ],
            [ "-(2+3) + 1",     "-4" ],
            [ "+(2+3) + 1",      "6" ],
        ];
    }

    /**
     * Tests whether native constants can be evaluated.
     * @param string $name The name of the constant to be retrieved.
     * @param float $expected The expected constant value.
     * @dataProvider provideNativeConstants
     * @since 3.0
     */
    public function testIfEvaluatesNativeConstants(string $name, float $expected)
    {
        $evaluated = $this->mathr->evaluate($name);

        $this->assertInstanceOf(NodeInterface::class, $evaluated);
        $this->assertInstanceOf(NumberNode::class, $evaluated);
        $this->assertEquals($expected, $evaluated->strRepr());
    }

    /**
     * Provides native constants and their expected values.
     * @return array[] The list of native constants.
     */
    public static function provideNativeConstants(): array
    {
        return [
            [ 'e',                 M_E, ],
            [ 'inf',               INF, ],
            [ 'pi',               M_PI, ],
            [ 'π',                M_PI, ],
            [ 'phi', 1.618033988749894, ],
            [ 'φ',   1.618033988749894, ],
            [ 'psi', 3.359885666243177, ],
            [ 'ψ',   3.359885666243177, ],
        ];
    }

    /**
     * Tests whether function expressions can be evaluated.
     * @param string $expression The expression to be tested.
     * @param string $expected The expected test result.
     * @dataProvider provideFunctionExpressions
     * @since 3.0
     */
    public function testIfEvaluatesFunctionExpressions(string $expression, string $expected)
    {
        $evaluated = $this->mathr->evaluate($expression);

        $this->assertInstanceOf(NodeInterface::class, $evaluated);
        $this->assertInstanceOf(NumberNode::class, $evaluated);
        $this->assertEquals($expected, $evaluated->strRepr());
    }

    /**
     * Provides expressions with native functions and their expected values.
     * @return array[] The list of expressions.
     */
    public static function provideFunctionExpressions(): array
    {
        return [
            [     'ln(e)',   '1' ],
            [    'cos(0)',   '1' ],
            [  'cos(π/2)',   '0' ],
            [    'cos(π)',  '-1' ],
            [ 'cos(3π/2)',   '0' ],
            [   'cos(2π)',   '1' ],
            [    'sin(0)',   '0' ],
            [  'sin(π/2)',   '1' ],
            [    'sin(π)',   '0' ],
            [ 'sin(3π/2)',  '-1' ],
            [   'sin(2π)',   '0' ],
            [    'tan(0)',   '0' ],
            [  'tan(π/4)',   '1' ],
        ];
    }

    /**
     * Tests whether expressions with unbound variables can be evaluated.
     * @param string $expression The expression to be tested.
     * @param string $expected The expected test result.
     * @dataProvider provideUnboundExpressions
     * @since 3.0
     */
    public function testIfEvaluatesUnboundExpressions(string $expression, string $expected)
    {
        $evaluated = $this->mathr->evaluate($expression);

        $this->assertInstanceOf(NodeInterface::class, $evaluated);
        $this->assertEquals($expected, (string) $evaluated);
        $this->assertEquals($expected, $evaluated->strRepr());
    }

    /**
     * Provides expressions with unbound variables.
     * @return string[][] The list of expressions.
     */
    public static function provideUnboundExpressions(): array
    {
        return [
            [ 'x + y +-z',      '(x + y) + -z' ],
            [ 'f(x, y+1)',       'f(x, y + 1)' ],
            [ '3f(x)g(y)', '(3 * f(x)) * g(y)' ],
            [ 'x + 3 * 2',             'x + 6' ],
        ];
    }

    /**
     * Tests whether constants can be stored and retrieved from memory.
     * @param string $decl The declaration of the constant to be stored in memory.
     * @param string $name  The name of the constant to retrieve from memory.
     * @param mixed $expected The expected constant's value in memory.
     * @dataProvider provideCustomConstants
     * @since 3.0
     */
    public function testIfCanStoreConstants(string $decl, string $name, mixed $expected)
    {
        $this->mathr->evaluate($decl);
        $evaluated = $this->mathr->evaluate($name);

        $this->assertInstanceOf(NodeInterface::class, $evaluated);
        $this->assertInstanceOf(NumberNode::class, $evaluated);
        $this->assertEquals($expected, $evaluated->strRepr());
    }

    /**
     * Provides constants to be stored in memory and their expected values.
     * @return array[] The list of constants.
     */
    public static function provideCustomConstants(): array
    {
        return [
            [ 'a = 10',    'a', '10' ],
            [ 'b = 2 + 4', 'b',  '6' ],
            [ 'c = ln(e)', 'c',  '1' ],
            [ 'd = e',     'd',  M_E ],
        ];
    }

    /**
     * Tests whether functions can be stored and retrieved from memory.
     * @param string[] $decls The list of functions declarations.
     * @param string[] $tests The list of tests for each function.
     * @dataProvider provideCustomFunctions
     * @since 3.0
     */
    public function testIfCanStoreFunctions(array $decls, array $tests)
    {
        foreach ($decls as $decl)
            $this->mathr->evaluate($decl);

        foreach ($tests as $expression => $expected) {
            $evaluated = $this->mathr->evaluate($expression);

            $this->assertInstanceOf(NodeInterface::class, $evaluated);
            $this->assertInstanceOf(NumberNode::class, $evaluated);
            $this->assertEquals($expected, $evaluated->strRepr());
        }
    }

    /**
     * Provides functions to be stored in memory and their expected bodies.
     * @return array[] The list of functions.
     */
    public static function provideCustomFunctions(): array
    {
        return [
            [
                [
                    'fib(0) = 0',
                    'fib(1) = 1',
                    'fib(n) = fib(n - 1) + fib(n - 2)',
                ],
                [
                    'fib(0)'  =>  '0',
                    'fib(1)'  =>  '1',
                    'fib(10)' => '55',
                ]
            ],
            [
                [
                    'fib(n) = (n ^ 3)',
                    'fib(n) = ceil((φ ^ n - (1 - φ) ^ n) / sqrt(5))',
                ],
                [
                    'fib(0)'  =>  '0',
                    'fib(1)'  =>  '1',
                    'fib(10)' => '55',
                ]
            ],
        ];
    }

    /**
     * Tests whether invalid assignments are refused.
     * @param string $expression The expression to be tested.
     * @dataProvider provideInvalidAssignments
     * @since 3.0
     */
    public function testIfRefusesInvalidAssignments(string $expression)
    {
        $this->expectException(AssignerException::class);
        $this->mathr->evaluate($expression);
    }

    /**
     * Provides invalid assignment expressions for testing.
     * @return string[][] The list of invalid assignments.
     */
    public static function provideInvalidAssignments(): array
    {
        return [
            [ 'f(g(x)) = x'  ],
            [ 'f(1 + 2) = 0' ],
            [ 'g(x = 1) = 1' ],
            [ 'x + y = 10'   ],
            [ 'x + 1 = 10'   ],
            [ '0 = 0'        ],
        ];
    }
}

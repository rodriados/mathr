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
     * Checks whether native constants can be evaluated.
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
}

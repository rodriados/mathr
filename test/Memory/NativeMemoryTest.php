<?php
/**
 * NativeMemory unit tests.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */

use Mathr\Evaluator\Memory;
use Mathr\Interperter\Token;
use Mathr\Evaluator\Node\NumberNode;
use Mathr\Evaluator\Node\FunctionNode;
use Mathr\Evaluator\Memory\NativeMemory;
use Mathr\Evaluator\Node\IdentifierNode;
use Mathr\Contracts\Evaluator\MemoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * The Mathr\Evaluator\Memory\NativeMemory test class.
 * @package Mathr
 */
final class NativeMemoryTest extends TestCase
{
    /**
     * The test's target memory for testing.
     * @var NativeMemory The memory instance.
     */
    protected NativeMemory $memory;

    /**
     * Sets the tests' environment up.
     * @since 3.0
     */
    protected function setUp(): void
    {
        $this->memory = new NativeMemory();
    }

    /**
     * Checks whether the memory has been successfully instantiated.
     * @since 3.0
     */
    public function testIfCanBeInstantiated()
    {
        $this->assertInstanceOf(Memory::class, $this->memory);
        $this->assertInstanceOf(MemoryInterface::class, $this->memory);
        $this->assertInstanceOf(NativeMemory::class, $this->memory);
    }

    /**
     * Checks whether the memory can get native constants.
     * @param string $name The name of the constant to be retrieved.
     * @param float $expected The expected constant value.
     * @dataProvider provideNativeConstants
     * @since 3.0
     */
    public function testIfCanGetNativeConstants(string $name, float $expected)
    {
        $node = new IdentifierNode(new Token($name));
        $this->assertEquals($expected, $this->memory->get($node));
    }

    /**
     * Checks whether the memory can get native functions.
     * @param string $name The name of the function to be retrieved.
     * @param array $args The list of arguments to execute the function with.
     * @param callable $expected The expected callable to retrieve from memory.
     * @dataProvider provideNativeFunctions
     * @since 3.0
     */
    public function testIfCanGetNativeFunctions(string $name, array $args, callable $expected)
    {
        $node = new FunctionNode(
            token: new Token($name),
            children: array_map(fn ($x) => new NumberNode(new Token($x)), $args)
        );

        $expected = $expected (...$args);
        $retrieved = $this->memory->get($node);

        if (is_nan($expected)) $this->assertNan($retrieved (...$args));
        else                   $this->assertEquals($expected, $retrieved (...$args));
    }

    /**
     * Provides native constants and their expected values.
     * @return array[] The list of native constants.
     */
    public function provideNativeConstants(): array
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
     * Provides native functions and their expected values.
     * @return array[] The list of native functions.
     */
    public function provideNativeFunctions(): array
    {
        return [
            [ 'abs',     [      -39 ],     'abs' ],
            [ 'abs',     [       48 ],     'abs' ],
            [ 'abs',     [     -3.9 ],     'abs' ],
            [ 'abs',     [      3.9 ],     'abs' ],
            [ 'acos',    [        3 ],    'acos' ],
            [ 'acos',    [     M_PI ],    'acos' ],
            [ 'acos',    [    -M_PI ],    'acos' ],
            [ 'acosh',   [        3 ],   'acosh' ],
            [ 'acosh',   [     M_PI ],   'acosh' ],
            [ 'acosh',   [    -M_PI ],   'acosh' ],
            [ 'asin',    [        3 ],    'asin' ],
            [ 'asin',    [     M_PI ],    'asin' ],
            [ 'asin',    [    -M_PI ],    'asin' ],
            [ 'asinh',   [        3 ],   'asinh' ],
            [ 'asinh',   [     M_PI ],   'asinh' ],
            [ 'asinh',   [    -M_PI ],   'asinh' ],
            [ 'atan',    [        3 ],    'atan' ],
            [ 'atan',    [     M_PI ],    'atan' ],
            [ 'atan',    [    -M_PI ],    'atan' ],
            [ 'atanh',   [        3 ],   'atanh' ],
            [ 'atanh',   [     M_PI ],   'atanh' ],
            [ 'atanh',   [    -M_PI ],   'atanh' ],
            [ 'ceil',    [      3.4 ],    'ceil' ],
            [ 'ceil',    [        3 ],    'ceil' ],
            [ 'ceil',    [     -3.4 ],    'ceil' ],
            [ 'ceil',    [       -3 ],    'ceil' ],
            [ 'cos',     [        3 ],     'cos' ],
            [ 'cos',     [     M_PI ],     'cos' ],
            [ 'cos',     [    -M_PI ],     'cos' ],
            [ 'cosh',    [        3 ],    'cosh' ],
            [ 'cosh',    [     M_PI ],    'cosh' ],
            [ 'cosh',    [    -M_PI ],    'cosh' ],
            [ 'deg2rad', [       90 ], 'deg2rad' ],
            [ 'deg2rad', [      -90 ], 'deg2rad' ],
            [ 'deg2rad', [      180 ], 'deg2rad' ],
            [ 'deg2rad', [     -180 ], 'deg2rad' ],
            [ 'floor',   [      3.4 ],   'floor' ],
            [ 'floor',   [        3 ],   'floor' ],
            [ 'floor',   [     -3.4 ],   'floor' ],
            [ 'floor',   [       -3 ],   'floor' ],
            [ 'rad2deg', [     M_PI ], 'rad2deg' ],
            [ 'rad2deg', [    -M_PI ], 'rad2deg' ],
            [ 'rad2deg', [ 2 * M_PI ], 'rad2deg' ],
            [ 'rad2deg', [ 3 * M_PI ], 'rad2deg' ],
            [ 'sin',     [        3 ],     'sin' ],
            [ 'sin',     [     M_PI ],     'sin' ],
            [ 'sin',     [    -M_PI ],     'sin' ],
            [ 'sinh',    [        3 ],    'sinh' ],
            [ 'sinh',    [     M_PI ],    'sinh' ],
            [ 'sinh',    [    -M_PI ],    'sinh' ],
            [ 'sqrt',    [        2 ],    'sqrt' ],
            [ 'sqrt',    [        3 ],    'sqrt' ],
            [ 'sqrt',    [        4 ],    'sqrt' ],
            [ 'sqrt',    [        5 ],    'sqrt' ],
            [ 'tan',     [        3 ],     'tan' ],
            [ 'tan',     [     M_PI ],     'tan' ],
            [ 'tan',     [    -M_PI ],     'tan' ],
            [ 'tanh',    [        3 ],    'tanh' ],
            [ 'tanh',    [     M_PI ],    'tanh' ],
            [ 'tanh',    [    -M_PI ],    'tanh' ],
            [ 'max',     [     1, 2 ],     'max' ],
            [ 'max',     [  1, 7, 2 ],     'max' ],
            [ 'max',     [  9, 4, 6 ],     'max' ],
            [ 'min',     [     7, 6 ],     'min' ],
            [ 'min',     [  8, 1, 9 ],     'min' ],
            [ 'min',     [  1, 9, 2 ],     'min' ],
            [ 'hypot',   [     2, 3 ],   'hypot' ],
            [ 'hypot',   [ 2.5, 3.5 ],   'hypot' ],
            [ 'hypot',   [   10, 11 ],   'hypot' ],
            [ 'log',     [        1 ],     'log' ],
            [ 'log',     [        2 ],     'log' ],
            [ 'log',     [       10 ],     'log' ],
            [ 'log',     [     0, 2 ],     'log' ],
            [ 'log',     [     8, 2 ],     'log' ],
            [ 'log',     [  100, 10 ],     'log' ],
            [ 'mod',     [ 2.5, 3.5 ],    'fmod' ],
            [ 'mod',     [ 118, 7.5 ],    'fmod' ],
            [ 'mod',     [ 2.5, 3.7 ],    'fmod' ],
            [ 'mod',     [    18, 7 ], fn ($x, $y) => $x % $y ],
            [ 'mod',     [   100, 9 ], fn ($x, $y) => $x % $y ],
            [ 'mod',     [   50, 25 ], fn ($x, $y) => $x % $y ],
            [ 'round',   [        2 ],   'round' ],
            [ 'round',   [      5.0 ],   'round' ],
            [ 'round',   [   7.8954 ],   'round' ],
            [ 'round',   [   9.9999 ],   'round' ],
            [ 'round',   [  18.0032 ],   'round' ],
            [ 'round',   [ 5.091, 2 ],   'round' ],
            [ 'round',   [ 5.099, 2 ],   'round' ],
            [ 'round',   [ 0.793, 1 ],   'round' ],
            [ 'round',   [ 0.223, 3 ],   'round' ],
            [ 'rt',      [     2, 2 ], fn ($x, $y) => pow($x, fdiv(1., $y)) ],
            [ 'rt',      [     8, 3 ], fn ($x, $y) => pow($x, fdiv(1., $y)) ],
            [ 'rt',      [    27, 3 ], fn ($x, $y) => pow($x, fdiv(1., $y)) ],
            [ 'rt',      [ 5.656, 3 ], fn ($x, $y) => pow($x, fdiv(1., $y)) ],
        ];
    }
}

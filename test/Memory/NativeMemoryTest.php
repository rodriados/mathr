<?php
/**
 * NativeMemory unit tests.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */

use Mathr\Evaluator\Memory;
use Mathr\Evaluator\Node\NumberNode;
use Mathr\Evaluator\Node\FunctionNode;
use Mathr\Evaluator\Memory\NativeMemory;
use Mathr\Evaluator\Node\IdentifierNode;
use Mathr\Contracts\Evaluator\MemoryException;
use Mathr\Contracts\Evaluator\MemoryInterface;
use PHPUnit\Framework\Attributes\DataProvider;
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
     * @since 3.0
     */
    #[DataProvider("provideNativeConstants")]
    public function testIfCanGetNativeConstants(string $name, float $expected)
    {
        $node = IdentifierNode::make($name);
        $this->assertEquals($expected, $this->memory->get($node));
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
     * Checks whether the memory can get native functions.
     * @param string $name The name of the function to be retrieved.
     * @param array $args The list of arguments to execute the function with.
     * @param callable $expected The expected callable to retrieve from memory.
     * @since 3.0
     */
    #[DataProvider("provideNativeFunctions")]
    public function testIfCanGetNativeFunctions(string $name, array $args, callable $expected, ?int $precision)
    {
        $parameters = array_map([NumberNode::class, 'make'], $args);
        $function = FunctionNode::make($name, $parameters);

        $expected = $expected (...$args);
        $retrieved = $this->memory->get($function);

        if (!is_null($precision))
            $expected = round($expected, $precision);

        if (is_nan($expected)) $this->assertNan($retrieved (...$args));
        else $this->assertEquals($expected, $retrieved (...$args));
    }

    /**
     * Checks whether immutability is preserved when trying to bind new nodes.
     * @since 3.0
     */
    public function testIfBindingsCannotBeCreated()
    {
        $this->expectException(MemoryException::class);
        $this->expectExceptionMessage('An immutable memory cannot be changed');
        $this->memory->put(IdentifierNode::make('value'), []);
    }

    /**
     * Checks whether immutability is preserved when trying to delete a binding.
     * @since 3.0
     */
    public function testIfBindingsCannotBeDeleted()
    {
        $this->expectException(MemoryException::class);
        $this->expectExceptionMessage('An immutable memory cannot be changed');
        $this->memory->delete(IdentifierNode::make('pi'));
    }

    /**
     * Provides native functions and their expected values.
     * @return array[] The list of native functions.
     */
    public static function provideNativeFunctions(): array
    {
        return [
            [ 'abs',     [      -39 ],     'abs', null ],
            [ 'abs',     [       48 ],     'abs', null ],
            [ 'abs',     [     -3.9 ],     'abs', null ],
            [ 'abs',     [      3.9 ],     'abs', null ],
            [ 'acos',    [        3 ],    'acos',   12 ],
            [ 'acos',    [     M_PI ],    'acos',   12 ],
            [ 'acos',    [    -M_PI ],    'acos',   12 ],
            [ 'acosh',   [        3 ],   'acosh',   12 ],
            [ 'acosh',   [     M_PI ],   'acosh',   12 ],
            [ 'acosh',   [    -M_PI ],   'acosh',   12 ],
            [ 'asin',    [        3 ],    'asin',   12 ],
            [ 'asin',    [     M_PI ],    'asin',   12 ],
            [ 'asin',    [    -M_PI ],    'asin',   12 ],
            [ 'asinh',   [        3 ],   'asinh',   12 ],
            [ 'asinh',   [     M_PI ],   'asinh',   12 ],
            [ 'asinh',   [    -M_PI ],   'asinh',   12 ],
            [ 'atan',    [        3 ],    'atan',   12 ],
            [ 'atan',    [     M_PI ],    'atan',   12 ],
            [ 'atan',    [    -M_PI ],    'atan',   12 ],
            [ 'atanh',   [        3 ],   'atanh',   12 ],
            [ 'atanh',   [     M_PI ],   'atanh',   12 ],
            [ 'atanh',   [    -M_PI ],   'atanh',   12 ],
            [ 'ceil',    [      3.4 ],    'ceil', null ],
            [ 'ceil',    [        3 ],    'ceil', null ],
            [ 'ceil',    [     -3.4 ],    'ceil', null ],
            [ 'ceil',    [       -3 ],    'ceil', null ],
            [ 'cos',     [        3 ],     'cos',   12 ],
            [ 'cos',     [     M_PI ],     'cos',   12 ],
            [ 'cos',     [    -M_PI ],     'cos',   12 ],
            [ 'cosh',    [        3 ],    'cosh',   12 ],
            [ 'cosh',    [     M_PI ],    'cosh',   12 ],
            [ 'cosh',    [    -M_PI ],    'cosh',   12 ],
            [ 'deg2rad', [       90 ], 'deg2rad',   12 ],
            [ 'deg2rad', [      -90 ], 'deg2rad',   12 ],
            [ 'deg2rad', [      180 ], 'deg2rad',   12 ],
            [ 'deg2rad', [     -180 ], 'deg2rad',   12 ],
            [ 'floor',   [      3.4 ],   'floor', null ],
            [ 'floor',   [        3 ],   'floor', null ],
            [ 'floor',   [     -3.4 ],   'floor', null ],
            [ 'floor',   [       -3 ],   'floor', null ],
            [ 'rad2deg', [     M_PI ], 'rad2deg',   12 ],
            [ 'rad2deg', [    -M_PI ], 'rad2deg',   12 ],
            [ 'rad2deg', [ 2 * M_PI ], 'rad2deg',   12 ],
            [ 'rad2deg', [ 3 * M_PI ], 'rad2deg',   12 ],
            [ 'sin',     [        3 ],     'sin',   12 ],
            [ 'sin',     [     M_PI ],     'sin',   12 ],
            [ 'sin',     [    -M_PI ],     'sin',   12 ],
            [ 'sinh',    [        3 ],    'sinh',   12 ],
            [ 'sinh',    [     M_PI ],    'sinh',   12 ],
            [ 'sinh',    [    -M_PI ],    'sinh',   12 ],
            [ 'sqrt',    [        2 ],    'sqrt',   12 ],
            [ 'sqrt',    [        3 ],    'sqrt',   12 ],
            [ 'sqrt',    [        4 ],    'sqrt',   12 ],
            [ 'sqrt',    [        5 ],    'sqrt',   12 ],
            [ 'tan',     [        3 ],     'tan',   12 ],
            [ 'tan',     [     M_PI ],     'tan',   12 ],
            [ 'tan',     [    -M_PI ],     'tan',   12 ],
            [ 'tanh',    [        3 ],    'tanh',   12 ],
            [ 'tanh',    [     M_PI ],    'tanh',   12 ],
            [ 'tanh',    [    -M_PI ],    'tanh',   12 ],
            [ 'max',     [     1, 2 ],     'max', null ],
            [ 'max',     [  1, 7, 2 ],     'max', null ],
            [ 'max',     [  9, 4, 6 ],     'max', null ],
            [ 'min',     [     7, 6 ],     'min', null ],
            [ 'min',     [  8, 1, 9 ],     'min', null ],
            [ 'min',     [  1, 9, 2 ],     'min', null ],
            [ 'hypot',   [     2, 3 ],   'hypot',   12 ],
            [ 'hypot',   [ 2.5, 3.5 ],   'hypot',   12 ],
            [ 'hypot',   [   10, 11 ],   'hypot',   12 ],
            [ 'log',     [        1 ],     'log',   12 ],
            [ 'log',     [        2 ],     'log',   12 ],
            [ 'log',     [       10 ],     'log',   12 ],
            [ 'log',     [     0, 2 ],     'log',   12 ],
            [ 'log',     [     8, 2 ],     'log',   12 ],
            [ 'log',     [  100, 10 ],     'log',   12 ],
            [ 'mod',     [ 2.5, 3.5 ],    'fmod',   12 ],
            [ 'mod',     [ 118, 7.5 ],    'fmod',   12 ],
            [ 'mod',     [ 2.5, 3.7 ],    'fmod',   12 ],
            [ 'mod',     [    18, 7 ], fn ($x, $y) => $x % $y, null ],
            [ 'mod',     [   100, 9 ], fn ($x, $y) => $x % $y, null ],
            [ 'mod',     [   50, 25 ], fn ($x, $y) => $x % $y, null ],
            [ 'round',   [        2 ],   'round', null ],
            [ 'round',   [      5.0 ],   'round', null ],
            [ 'round',   [   7.8954 ],   'round', null ],
            [ 'round',   [   9.9999 ],   'round', null ],
            [ 'round',   [  18.0032 ],   'round', null ],
            [ 'round',   [ 5.091, 2 ],   'round', null ],
            [ 'round',   [ 5.099, 2 ],   'round', null ],
            [ 'round',   [ 0.793, 1 ],   'round', null ],
            [ 'round',   [ 0.223, 3 ],   'round', null ],
            [ 'rt',      [     2, 2 ], fn ($x, $y) => pow($x, fdiv(1., $y)), 12 ],
            [ 'rt',      [     8, 3 ], fn ($x, $y) => pow($x, fdiv(1., $y)), 12 ],
            [ 'rt',      [    27, 3 ], fn ($x, $y) => pow($x, fdiv(1., $y)), 12 ],
            [ 'rt',      [ 5.656, 3 ], fn ($x, $y) => pow($x, fdiv(1., $y)), 12 ],
        ];
    }
}

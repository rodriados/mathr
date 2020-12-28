<?php
/**
 * The native memory with global functions and constants.
 * @package Mathr\Evaluator\Memory
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator\Memory;

use Mathr\Evaluator\Memory;
use Mathr\Contracts\Evaluator\StorableNodeInterface;

/**
 * A constant memory with global functions and constants.
 * @package Mathr\Evaluator\Memory
 */
class NativeMemory extends Memory
{
    use ImmutableMemory;

    /**
     * The list of native constants and functions.
     * @var array The map of native constants and functions.
     */
    private array $mapping;

    /**
     * NativeMemory constructor.
     * @param array $mapping The list of native constants and functions.
     */
    public function __construct(
        array $mapping = [],
    ) {
        // The native memory has no parent. It should always be the highest
        // in each and every memory hierarchy.
        parent::__construct(null);

        $this->mapping = $mapping ?: array_merge(
            static::listOfConstants(),
            static::listOfFunctions()
        );
    }

    /**
     * Retrieves a function or variable from the memory.
     * @param StorableNodeInterface $node The node to retrieve the contents of.
     * @return mixed The requested node's contents.
     */
    public function get(StorableNodeInterface $node): mixed
    {
        $storageId = $node->getStorageId();
        return $this->retrieveBinding($storageId);
    }

    /**
     * Informs the list of native variables or constants.
     * @return array The list of variables and constants.
     */
    protected static function listOfConstants(): array
    {
        return [
            '$e'    => M_E,
            '$inf'  => INF,
            '$pi'   => M_PI,
            '$π'    => M_PI,
            '$phi'  => 1.618033988749894,
            '$φ'    => 1.618033988749894,
            '$psi'  => 3.359885666243177,
            '$ψ'    => 3.359885666243177,
        ];
    }

    /**
     * Informs the list of native functions.
     * @return array The list of functions.
     */
    protected static function listOfFunctions(): array
    {
        return [
            'abs@1'     => fn ($x) => abs($x),
            'acos@1'    => fn ($x) => acos($x),
            'acosh@1'   => fn ($x) => acosh($x),
            'asin@1'    => fn ($x) => asin($x),
            'asinh@1'   => fn ($x) => asinh($x),
            'atan@1'    => fn ($x) => atan($x),
            'atanh@1'   => fn ($x) => atanh($x),
            'ceil@1'    => fn ($x) => ceil($x),
            'cos@1'     => fn ($x) => cos($x),
            'cosh@1'    => fn ($x) => cosh($x),
            'deg2rad@1' => fn ($x) => deg2rad($x),
            'floor@1'   => fn ($x) => floor($x),
            'rad2deg@1' => fn ($x) => rad2deg($x),
            'sin@1'     => fn ($x) => sin($x),
            'sinh@1'    => fn ($x) => sinh($x),
            'sqrt@1'    => fn ($x) => sqrt($x),
            'tan@1'     => fn ($x) => tan($x),
            'tanh@1'    => fn ($x) => tanh($x),
            'max@*'     => fn (...$x) => max(...$x),
            'min@*'     => fn (...$x) => min(...$x),
            'hypot@2'   => fn ($x, $y) => hypot($x, $y),
            'log@1'     => fn ($x) => log($x, M_E),
            'log@2'     => fn ($x, $e) => log($x, $e),
            'mod@2'     => fn ($x, $y) => (is_float($x) || is_float($y)) ? fmod($x, $y) : ($x % $y),
            'rand@0'    => fn () => rand(),
            'rand@1'    => fn ($x) => rand($x),
            'rand@2'    => fn ($x, $y) => rand($x, $y),
            'round@1'   => fn ($x) => round($x),
            'round@2'   => fn ($x, $y) => round($x, $y),
            'rt@2'      => fn ($x, $y) => pow($x, fdiv(1., $y)),
        ];
    }

    /**
     * Retrieves a native binding from the memory.
     * @param string $binding The binding to be retrieved.
     * @return mixed The retrieved binding.
     */
    private function retrieveBinding(string $binding): mixed
    {
        $lookup = !array_key_exists($binding, $this->mapping)
            ? preg_replace('/@\d+$/', '@*', $binding)
            : $binding;

        return $this->mapping[$lookup] ?? null;
    }
}

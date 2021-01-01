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
            'acos@1'    => fn ($x) => round( acos($x), 12),
            'acosh@1'   => fn ($x) => round(acosh($x), 12),
            'asin@1'    => fn ($x) => round( asin($x), 12),
            'asinh@1'   => fn ($x) => round(asinh($x), 12),
            'atan@1'    => fn ($x) => round( atan($x), 12),
            'atanh@1'   => fn ($x) => round(atanh($x), 12),
            'ceil@1'    => fn ($x) => ceil($x),
            'cos@1'     => fn ($x) => round(    cos($x), 12),
            'cosh@1'    => fn ($x) => round(   cosh($x), 12),
            'deg2rad@1' => fn ($x) => round(deg2rad($x), 12),
            'floor@1'   => fn ($x) => floor($x),
            'rad2deg@1' => fn ($x) => round(rad2deg($x), 12),
            'sin@1'     => fn ($x) => round(    sin($x), 12),
            'sinh@1'    => fn ($x) => round(   sinh($x), 12),
            'sqrt@1'    => fn ($x) => round(sqrt($x), 12),
            'tan@1'     => fn ($x) => round( tan($x), 12),
            'tanh@1'    => fn ($x) => round(tanh($x), 12),
            'max@*'     => fn (...$x) => max(...$x),
            'min@*'     => fn (...$x) => min(...$x),
            'hypot@2'   => fn ($x, $y) => round(hypot($x, $y), 12),
            'ln@1'      => fn ($x) => round( log($x, M_E), 12),
            'log@1'     => fn ($x) => round( log($x, M_E), 12),
            'log@2'     => fn ($x, $e) => round(  log($x, $e), 12),
            'mod@2'     => fn ($x, $y) => round( fmod($x, $y), 12),
            'round@1'   => fn ($x) => round($x),
            'round@2'   => fn ($x, $y) => round($x, $y),
            'rt@2'      => fn ($x, $y) => round(pow($x, fdiv(1., $y)), 12),
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

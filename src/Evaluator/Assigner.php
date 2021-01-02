<?php
/**
 * Binder for storable nodes in memory.
 * @package Mathr\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator;

use Mathr\Evaluator\Node\NumberNode;
use Mathr\Evaluator\Node\HierarchyNode;
use Mathr\Evaluator\Node\IdentifierNode;
use Mathr\Evaluator\Memory\ScopeMemory;
use Mathr\Contracts\Evaluator\NodeInterface;
use Mathr\Contracts\Evaluator\MemoryException;
use Mathr\Contracts\Evaluator\MemoryInterface;
use Mathr\Contracts\Evaluator\AssignerException;
use Mathr\Contracts\Evaluator\StorableNodeInterface;

/**
 * Helper object for assigning storable node into memory.
 * @package Mathr\Evaluator
 */
class Assigner
{
    /**
     * Assings the contents to the binding on the given memory instance.
     * @param MemoryInterface $memory The memory in which the assignment must occur.
     * @param NodeInterface $binding The binding to the given content.
     * @param mixed $contents The content to be bound.
     * @return StorableNodeInterface The given assignment binding.
     * @throws AssignerException An invalid binding was given.
     * @throws MemoryException The memory stack was overflown while evaluating contents.
     */
    public static function assignToMemory(
        MemoryInterface $memory,
        NodeInterface $binding,
        mixed $contents
    ): StorableNodeInterface
    {
        if (!$binding instanceof StorableNodeInterface)
            throw AssignerException::assignmentIsInvalid($binding);

        if ($contents instanceof NodeInterface)
            $contents = $binding instanceof HierarchyNode
                ? self::prepareBinding($memory, $binding, $contents)
                : $contents->evaluate($memory);

        $memory->put($binding, $contents);
        return $binding;
    }

    /**
     * Prepares the binding and its contents to be stored in memory.
     * @param MemoryInterface $memory The memory in which the binding will be stored.
     * @param HierarchyNode|StorableNodeInterface $binding The binding instance.
     * @param NodeInterface $contents The content to be bound.
     * @return array The prepared binding with its possible overloads.
     * @throws AssignerException The binding's parameters have invalid types.
     * @throws MemoryException The memory stack was overflown while evaluating contents.
     */
    private static function prepareBinding(
        MemoryInterface $memory,
        HierarchyNode|StorableNodeInterface $binding,
        NodeInterface $contents
    ): array
    {
        $hierarchy = $binding->getHierarchy();

        if (!self::validateParamTypes($hierarchy))
            throw AssignerException::bindingParamsAreInvalid($binding);

        for ($i = 0; isset($hierarchy[$i]) && $hierarchy[$i] instanceof IdentifierNode; ++$i)
            $mappings[$hierarchy[$i]->getStorageId()] = IdentifierNode::make("#{$i}");

        $frame    = new ScopeMemory($memory);

        $contents = $contents->evaluate($frame->pushFrame($mappings ?? []));
        $previous = $memory->get($binding) ?: [];

        return self::addToBindingList($previous, $hierarchy, $contents);
    }

    /**
     * The required parameter types and order.
     * Informs which types are accepted as binding parameters, and in which order.
     */
    private const PARAMETER_ORDER = [ IdentifierNode::class, NumberNode::class ];

    /**
     * Checks whether the given parameter types are valid.
     * @param NodeInterface[] $params The binding parameters.
     * @return bool Are all parameter types valid?
     */
    private static function validateParamTypes(array $params): bool
    {
        $order = self::PARAMETER_ORDER;

        for ($i = 0; !empty($order) && isset($params[$i]); ++$i)
            while (!empty($order) && !is_a($params[$i], $order[0]))
                array_shift($order);

        return !empty($order);
    }

    /**
     * Adds the given binding and its contents to the list of bindings in memory.
     * @param NodeInterface[][] $previous The list of bindings currently in memory.
     * @param NodeInterface[] $params The binding to be added to the memory.
     * @param NodeInterface $contents The binding contents.
     * @return NodeInterface[][] The new bindings list.
     */
    private static function addToBindingList(array $previous, array $params, NodeInterface $contents)
        : array
    {
        $entry = [$params, $contents];
        [$index, $modify] = self::findIndexInBindingList($previous, $params);

        array_splice($previous, $index, (int) $modify, [ $entry ] );
        return $previous;
    }

    /**
     * Finds the index at which the given binding parameters should be stored on
     * the list of current bindings in memory.
     * @param NodeInterface[][] $bindings The list of bindings currently in memory.
     * @param NodeInterface[] $params The binding parameters to find the index of.
     * @return array The index and whether previous binding should be modified.
     */
    private static function findIndexInBindingList(array $bindings, array $params): array
    {
        foreach ($bindings as $index => [$decl, ])
            if (($comparision = self::compareBindings($decl, $params)) <= 0)
                return [ $index, $comparision == 0 ];

        return [ count($bindings), false ];
    }

    /**
     * Compares two bindings and informs their respective ordering.
     * @param NodeInterface[] $one The first binding to be compared.
     * @param NodeInterface[] $two The second binding to be compared.
     * @return int The binding comparision result.
     */
    private static function compareBindings(array $one, array $two): int
    {
        $diff = count($one) <=> count($two);

        for ($i = 0; !$diff && isset($one[$i], $two[$i]); ++$i)
            $diff = self::compareBindingParam($one[$i], $two[$i]);

        return $diff;
    }

    /**
     * Compares two binding parameters and informs their ordering.
     * @param NodeInterface $one The first parameter to be compared.
     * @param NodeInterface $two The second parameter to be compared.
     * @return int The parameters comparision result.
     */
    private static function compareBindingParam(NodeInterface $one, NodeInterface $two): int
    {
        if (is_a($one, self::PARAMETER_ORDER[0]) && is_a($two, self::PARAMETER_ORDER[0])) return  0;
        if (is_a($one, self::PARAMETER_ORDER[0]) && is_a($two, self::PARAMETER_ORDER[1])) return -1;
        if (is_a($one, self::PARAMETER_ORDER[1]) && is_a($two, self::PARAMETER_ORDER[0])) return +1;

        return $two->getData() <=> $one->getData();
    }
}

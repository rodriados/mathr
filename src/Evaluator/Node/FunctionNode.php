<?php
/**
 * Node for functions.
 * @package Mathr\Evaluator\Node
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator\Node;

use Mathr\Interperter\Token;
use Mathr\Contracts\Evaluator\NodeException;
use Mathr\Contracts\Evaluator\NodeInterface;
use Mathr\Contracts\Evaluator\MemoryException;
use Mathr\Contracts\Evaluator\MemoryInterface;
use Mathr\Contracts\Evaluator\EvaluationException;
use Mathr\Contracts\Evaluator\MemoryStackInterface;
use Mathr\Contracts\Evaluator\StorableNodeInterface;

/**
 * Represents a function in an expression node.
 * @package Mathr\Evaluator\Node
 */
class FunctionNode extends HierarchyNode implements StorableNodeInterface
{
    /**
     * Retrieves the data represented by the node.
     * @return string The node's internal data.
     */
    public function getData(): string
    {
        return str_replace('(', '', $this->token->getData());
    }

    /**
     * Informs the node's storage id for lookup and storage in memories.
     * @return string The node's storage id.
     */
    public function getStorageId(): string
    {
        return sprintf('%s@%d', $this->getData(), $this->getHierarchyCount());
    }

    /**
     * Gives access to the parameters given to the function.
     * @return NodeInterface[] The function's parameters.
     */
    public function getParameters(): array
    {
        return $this->getHierarchy();
    }

    /**
     * Evaluates the node and produces a result.
     * @param MemoryInterface $memory The memory to lookup for bindings.
     * @return NodeInterface The produced resulting node.
     * @throws EvaluationException Binding not found or invalid memory.
     * @throws NodeException The produced output is invalid.
     * @throws MemoryException The function call-stack has overflown.
     */
    public function evaluate(MemoryInterface $memory): NodeInterface
    {
        $hierarchy = $this->evaluateHierarchy($memory);

        return self::allOfNumbers($hierarchy)
            ? $this->evaluateBinding($memory, $hierarchy)
            : static::make($this->getData(), $hierarchy);
    }

    /**
     * Represents the node as a string.
     * @return string The node's string representation.
     */
    public function strRepr(): string
    {
        $hierarchy = $this->getHierarchy();
        return sprintf('%s(%s)', $this->getData(), static::strHierarchy($hierarchy));
    }

    /**
     * Creates a new node from an identifier and parameters.
     * @param string $name The name of function to create the node for.
     * @param NodeInterface[] $args The function's arguments.
     * @return static The created node.
     */
    public static function make(string $name, array $args): static
    {
        $token = new Token($name, type: Token::FUNCTION);
        return new static($token, $args);
    }

    /**
     * Looks up for a bound function in memory and evaluates it.
     * @param MemoryInterface $memory The memory to lookup for bindings.
     * @param NodeInterface[] $params The function's parameters.
     * @return NumberNode The produced resulting node.
     * @throws EvaluationException Binding not found or invalid memory.
     * @throws NodeException An invalid output was produced.
     * @throws MemoryException The call-stack has overflown.
     */
    private function evaluateBinding(MemoryInterface $memory, array $params): NodeInterface
    {
        $binding = $memory->get($this) ?: [];

        if (is_array($binding) && !is_callable($binding))
            $binding = $this->pickBinding($binding, $params);

        if (!$binding instanceof NodeInterface)
            return self::evaluateNativeFunction($binding, $params);

        if (!$memory instanceof MemoryStackInterface)
            throw EvaluationException::functionExpectedStackMemory();

        return self::evaluateCustomFunction($memory, $binding, $params);
    }

    /**
     * Retrieves all functions bound to the name of the current function and picks
     * the one which best matches with the given parameters.
     * @param NodeInterface[] $bindings The function bindings retrieved from memory.
     * @param NodeInterface[] $params The function's paramenters.
     * @return NodeInterface The best matched binding.
     * @throws EvaluationException No binding found for the given function.
     */
    private function pickBinding(array $bindings, array $params): NodeInterface
    {
        foreach ($bindings as [$decl, $body])
            if (count($decl) == count($params))
                if (self::matchFunction($decl, $params))
                    return $body;

        throw EvaluationException::functionIsNotFound($this);
    }

    /**
     * Informs whether the given parameters match with the given declaration.
     * @param NodeInterface[] $decl The declaration to compare parameters with.
     * @param NodeInterface[] $params The current function's parameters.
     * @return bool Does the declaration match with the parameters?
     */
    private static function matchFunction(array $decl, array $params): bool
    {
        foreach (array_reverse($params, true) as $i => $param)
            if (!$decl[$i] instanceof IdentifierNode)
                if ($decl[$i]->getData() != $param->getData())
                    return false;

        return true;
    }

    /**
     * Evaluates a native function with the given parameters.
     * @param callable $binding The native function binding.
     * @param NodeInterface[] $params The function's parameters.
     * @return NumberNode The produced evaluation result.
     * @throws NodeException The produced result is invalid.
     */
    private static function evaluateNativeFunction(callable $binding, array $params): NumberNode
    {
        $lambda = fn (NumberNode $param) => $param->getData();
        return NumberNode::make(($binding)(...array_map($lambda, $params)));
    }

    /**
     * Evaluates a custom function with the given parameters.
     * @param MemoryStackInterface $memory The memory to lookup for bindings.
     * @param NodeInterface $funcbody The function's body to be executed.
     * @param NodeInterface[] $params The function's parameters.
     * @return NodeInterface The produced evaluation result.
     * @throws MemoryException The call-stack has overflown.
     */
    private static function evaluateCustomFunction(
        MemoryStackInterface $memory,
        NodeInterface $funcbody,
        array $params = []
    ): NodeInterface
    {
        $frame  = $memory->pushFrame(self::bindParameters($params));
        $result = $funcbody->evaluate($frame);
                  $memory->popFrame();

        return $result;
    }

    /**
     * Converts the given function parameters to memory frame bindings.
     * @param NodeInterface[] $params The parameters to be bound to the stack frame.
     * @return NodeInterface[] The bound stack frame parameters.
     */
    private static function bindParameters(array $params): array
    {
        foreach ($params as $i => $param) {
            $identifier = IdentifierNode::make("#{$i}");
            $bindings[$identifier->getStorageId()] = $param;
        }

        return $bindings ?? [];
    }
}

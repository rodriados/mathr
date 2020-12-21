<?php
/**
 * The memory for scoped functions and variables.
 * @package Mathr\Evaluator\Memory
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator\Memory;

use Mathr\Parser\Node\FunctionNode;
use Mathr\Parser\Node\VariableNode;
use Mathr\Parser\Node\NodeInterface;
use Mathr\Parser\Node\BindableNodeInterface;
use Mathr\Parser\Node\EvaluableNodeInterface;

/**
 * Keeps track of a scope's variables and functions.
 * @package Mathr\Evaluator\Memory
 */
class ScopeMemory extends Memory implements MemoryFrameInterface
{
    /**
     * The current function call-depth.
     * @var int The memory frame's current call-depth.
     */
    private int $depth = 0;

    /**
     * The function call memory frames.
     * @var array The list of memory frames managed by the scoped memory.
     */
    private array $frames = [];

    /**
     * The list of scope's variables and functions.
     * @var array The map of variables and functions.
     */
    private array $mapping = [];

    /**
     * ScopedMemory constructor.
     * @param Memory|null $parent The parent memory frame.
     * @param int $maxDepth The maximum function-call depth of the new memory frame.
     */
    public function __construct(
        ?Memory $parent = null,
        private int $maxDepth = 20,
    ) {
        parent::__construct($parent);
    }

    /**
     * Retrieves a function or variable from the memory.
     * @param BindableNodeInterface $node The node to retrieve the contents of.
     * @return BindableNodeInterface|null The node bound to the respective contents.
     * @throws MemoryException The node binding was rejected.
     */
    public function get(BindableNodeInterface $node): ?BindableNodeInterface
    {
        return $this->bindNode($node, $this->frames[$this->depth] ?? [])
            ?? $this->bindNode($node, $this->mapping)
            ?? $this->parent?->get($node);
    }

    /**
     * Puts the contents of a function or variable into memory.
     * @param BindableNodeInterface $node The node to put the contents of.
     * @param EvaluableNodeInterface $contents The target node's contents.
     */
    public function put(BindableNodeInterface $node, EvaluableNodeInterface $contents): void
    {
        if ($node instanceof FunctionNode) {
            $this->mapping[(string) $node][] = $contents;
        } elseif ($node instanceof VariableNode) {
            $this->mapping[(string) $node] = $contents;
        }
    }

    /**
     * Removes a function or variable from the memory.
     * @param BindableNodeInterface $node The node to be removed.
     */
    public function delete(BindableNodeInterface $node): void
    {
        if (isset($this->mapping[(string) $node])) {
            unset($this->mapping[(string) $node]);
        }
    }

    /**
     * Pushes scope bindings into a new memory frame.
     * @param NodeInterface ...$args The new memory frame scope bindings.
     * @return MemoryFrameInterface The newly created memory frame.
     * @throws MemoryException The function call stack is too deep.
     */
    public function pushFrame(NodeInterface ...$args): MemoryFrameInterface
    {
        if (++$this->depth > $this->maxDepth)
            throw MemoryException::stackOverflow();

        foreach ($args as $id => $node)
            $this->frames[$this->depth]["$@{$id}"] = $node;

        return $this;
    }

    /**
     * Pops the last memory frame from the stack.
     * @throws MemoryException There are no frames to be popped.
     */
    public function popFrame(): void
    {
        if (empty($this->frames) || $this->depth < 1)
            throw MemoryException::noStackFrame();

        unset($this->frames[$this->depth--]);
    }

    /**
     * Tries to binds a node to a value on given scope.
     * @param BindableNodeInterface $node The node to be bound.
     * @param array $scope The scope to search for the node binding.
     * @return BindableNodeInterface|null The resulting bound node.
     * @throws MemoryException The node binding was rejected.
     */
    private function bindNode(BindableNodeInterface $node, array $scope): ?BindableNodeInterface
    {
        $request = (string) $node;
        $binding = $scope[$request] ?? null;

        if (!is_null($binding))
            $node->bind($binding);

        return $binding ? $node : null;
    }
}

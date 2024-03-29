<?php
/**
 * Mathr's interpreter and evaluator integration.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr;

use Exception;
use Serializable;
use Mathr\Evaluator\Assigner;
use Mathr\Evaluator\Memory\ScopeMemory;
use Mathr\Evaluator\Memory\NativeMemory;
use Mathr\Interperter\Parser\DefaultParser;
use Mathr\Contracts\Evaluator\NodeInterface;
use Mathr\Contracts\Evaluator\MemoryInterface;
use Mathr\Contracts\Evaluator\MemoryException;
use Mathr\Contracts\Interperter\ParserException;
use Mathr\Contracts\Interperter\ParserInterface;
use Mathr\Contracts\Evaluator\AssignerException;
use Mathr\Interperter\Tokenizer\DefaultTokenizer;
use Mathr\Contracts\Evaluator\StorableNodeInterface;

/**
 * Parses and evaluates expresions, and manages expression bindings.
 * @package Mathr
 */
class Mathr
{
    /**
     * Mathr constructor.
     * @param MemoryInterface|null $memory The binding memory instance.
     * @param ParserInterface|null $parser The expression parser instance.
     */
    public function __construct(
        protected ?MemoryInterface $memory = null,
        protected ?ParserInterface $parser = null,
    ) {
        $this->memory = $memory ?: new ScopeMemory(new NativeMemory);
        $this->parser = $parser ?: new DefaultParser(new DefaultTokenizer);
    }

    /**
     * Evaluates the given expression and produces a result.
     * @param string $expression The expression to be evaluated.
     * @return NodeInterface The produced resulting node.
     * @throws ParserException The expression is invalid.
     */
    public function evaluate(string $expression): NodeInterface
    {
        return $this->parser
            ->runParser($expression)
            ->getExpression()
            ->evaluate($this->memory);
    }

    /**
     * Assigns an expression to the given declaration.
     * @param string $decl The declaration to be assigned to.
     * @param mixed $expression The expression to be bound.
     * @throws AssignerException The given declaration is invalid.
     * @throws ParserException The given expression is invalid.
     * @throws MemoryException The memory stack was overflown while evaluating contents.
     */
    public function set(string $decl, mixed $expression): void
    {
        $decl = $this->parser->runParser($decl)->getRaw();

        if (!is_callable($expression) && (is_string($expression) || is_numeric($expression)))
            $expression = $this->parser->runParser($expression)->getRaw();

        Assigner::assignToMemory($this->memory, $decl, $expression);
    }

    /**
     * Removes a variable or function binding from the memory.
     * @param string $decl The declaration to be removed from memory.
     * @throws AssignerException The given declaration is invalid.
     * @throws ParserException The given expression is invalid.
     */
    public function delete(string $decl): void
    {
        $decl = $this->parser->runParser($decl)->getRaw();

        if (!$decl instanceof StorableNodeInterface)
            throw AssignerException::assignmentIsInvalid($decl);

        $this->memory->delete($decl);
    }

    /**
     * Exports the memory's bindings as a serialized string.
     * @return string The memory's serialization.
     * @throws MemoryException The memory cannot be exported.
     */
    public function export(): string
    {
        if (!$this->memory instanceof Serializable)
            throw MemoryException::memoryCannotBeSerialized();

        try {
            return serialize($this->memory);
        } catch (Exception) {
            throw MemoryException::memoryCannotBeSerialized();
        }
    }

    /**
     * Imports memory bindings from a serialized string.
     * @param string $serialized The memory's serialized string.
     */
    public function import(string $serialized): void
    {
        $memory = unserialize($serialized);
        $memory->setParentMemory($this->memory->getParentMemory());
        $this->memory = $memory;
    }
}

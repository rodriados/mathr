<?php
/**
 * Mathr project entry file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr;

use Mathr\Evaluator\Memory\ScopeMemory;
use Mathr\Evaluator\Memory\NativeMemory;
use Mathr\Interperter\Parser\DefaultParser;
use Mathr\Contracts\Evaluator\NodeInterface;
use Mathr\Contracts\Evaluator\MemoryInterface;
use Mathr\Contracts\Interperter\ParserException;
use Mathr\Contracts\Interperter\ParserInterface;
use Mathr\Interperter\Tokenizer\DefaultTokenizer;

/**
 * Parses and evaluates expresions, and manages expression bindings.
 * @package Mathr
 */
class Mathr
{
    /**
     * The memory instance for binding identifiers and functions.
     * @var MemoryInterface The memory instance.
     */
    protected MemoryInterface $memory;

    /**
     * The parser instance for parsing expressions into evaluable nodes.
     * @var ParserInterface The parser instance.
     */
    protected ParserInterface $parser;

    /**
     * Mathr constructor.
     * @param MemoryInterface|null $memory The binding memory instance.
     * @param ParserInterface|null $parser The expression parser instance.
     */
    public function __construct(
        ?MemoryInterface $memory = null,
        ?ParserInterface $parser = null,
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
}

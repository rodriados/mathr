<?php
/**
 * The basics needed for a parser.
 * @package Mathr\Contracts\Interperter
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Contracts\Interperter;

use Mathr\Contracts\Evaluator\ExpressionBuilderInterface;

/**
 * Lists the methods needed by an expression parser.
 * @package Mathr\Contracts\Interperter
 */
interface ParserInterface
{
    /**
     * Parses the given expression.
     * @param string $expression The expression to be parsed.
     * @return ExpressionBuilderInterface The expression builder instance.
     */
    public function runParser(string $expression): ExpressionBuilderInterface;
}

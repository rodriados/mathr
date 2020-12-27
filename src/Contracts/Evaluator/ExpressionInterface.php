<?php
/**
 * The basics for an evaluable expression.
 * @package Mathr\Contracts\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Contracts\Evaluator;

/**
 * Represents an evaluable expression.
 * @package Mathr\Contracts\Evaluator
 */
interface ExpressionInterface
{
    /**
     * Represents the node as a string.
     * @return string The node's string representation.
     */
    public function strRepr(): string;
}

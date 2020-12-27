<?php
/**
 * Basic node representation.
 * @package Mathr\Contracts\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Contracts\Evaluator;

use Mathr\Contracts\Interperter\TokenInterface;

/**
 * Represents methods commom to all nodes.
 * @package Mathr\Contracts\Evaluator
 */
interface NodeInterface
{
    /**
     * Retrieves the data represented by the node.
     * @return string The node's internal data.
     */
    public function getData(): string;

    /**
     * Returns the token represented by the node.
     * @return TokenInterface The node's representation token.
     */
    public function getToken(): TokenInterface;

    /**
     * Represents the node as a string.
     * @return string The node's string representation.
     */
    public function strRepr(): string;
}

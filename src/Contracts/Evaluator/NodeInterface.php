<?php
/**
 * Basic node representation.
 * @package Mathr\Contracts\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Contracts\Evaluator;

use Serializable;
use Mathr\Contracts\Interperter\TokenInterface;

/**
 * Represents methods commom to all nodes.
 * @package Mathr\Contracts\Evaluator
 */
interface NodeInterface extends ExpressionInterface, Serializable
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
}

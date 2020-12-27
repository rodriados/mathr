<?php
/**
 * Node for generic identifiers.
 * @package Mathr\Evaluator\Node
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Evaluator\Node;

use Mathr\Evaluator\Node;
use Mathr\Contracts\Evaluator\StorableNodeInterface;

/**
 * Represents an identifier reference in an expression node.
 * @package Mathr\Evaluator\Node
 */
class IdentifierNode extends Node implements StorableNodeInterface
{
    /**
     * Informs the node's storage id for lookup and storage in memories.
     * @return string The node's storage id.
     */
    public function getStorageId(): string
    {
        return "\${$this->getData()}";
    }
}

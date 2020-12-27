<?php
/**
 * Nodes that can be bound in a memory storage.
 * @package Mathr\Contracts\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Contracts\Evaluator;

/**
 * Represents the minimal needed by a node to be stored.
 * @package Mathr\Contracts\Evaluator
 */
interface StorableNodeInterface extends NodeInterface
{
    /**
     * Informs the node's storage id for lookup and storage in memories.
     * @return string The node's storage id.
     */
    public function getStorageId(): string;
}

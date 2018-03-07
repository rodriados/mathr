<?php
/**
 * Mathr\Node\NodeInterface interface file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017-2018 Rodrigo Siqueira
 */
namespace Mathr\Node;

use Mathr\Node;
use Mathr\Scope;

interface NodeInterface
{
	/**
	 * Evaluates this node and returns its result.
	 * @param Scope $scope Storage for variables and functions.
	 * @return Node Resulting node.
	 */
	public function evaluate(Scope $scope): Node;
}

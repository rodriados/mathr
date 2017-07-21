<?php
/**
 * Mathr\Node\AbstractNode class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr\Node;

use Mathr\Scope;
use Mathr\Parser\Token;
use SplStack as Stack;

abstract class AbstractNode
{
	/**
	 * Node's internal value.
	 * @var mixed Value held by node.
	 */
	protected $value;
	
	/**
	 * Represents this node as a string.
	 * @return string Node's string representation.
	 */
	public function __toString()
	{
		return (string)($this->value);
	}
	
	/**
	 * Returns this node value.
	 * @return mixed Node's internal value.
	 */
	public function value()
	{
		return $this->value;
	}
	
	/**
	 * Evaluates this node and returns its result.
	 * @param Scope $scope Storage for variables and functions.
	 * @return AbstractNode Resulting node.
	 */
	abstract public function evaluate(Scope $scope) : AbstractNode;
	
	/**
	 * Creates a node based in a token.
	 * @param Token $token Token from which node is built from.
	 * @param Stack $stack Input stack.
	 * @return AbstractNode Created node.
	 */
	abstract public static function fromToken(Token $token, Stack $stack) : AbstractNode;
	
}

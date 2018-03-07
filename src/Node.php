<?php
/**
 * Mathr\Node abstract class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017-2018 Rodrigo Siqueira
 */
namespace Mathr;

abstract class Node
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
	final public function __toString()
	{
		return (string)($this->getValue());
	}
	
	/**
	 * Returns this node's value.
	 * @return mixed Node's internal value.
	 */
	public function getValue()
	{
		return $this->value;
	}
	
	/**
	 * Evaluates this node and returns its result.
	 * @param Scope $scope Storage for variables and functions.
	 * @return Node Resulting node.
	 */
	abstract public function evaluate(Scope $scope): Node;
	
	/**
	 * Creates a node based in a token.
	 * @param Token $token Token from which node is built from.
	 * @param \SplStack $stack Input stack.
	 * @return Node Created node.
	 */
	abstract public static function fromToken(Token $token, \SplStack $stack): Node;
	
}

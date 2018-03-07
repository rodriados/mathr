<?php
/**
 * Mathr\Node abstract class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017-2018 Rodrigo Siqueira
 */
namespace Mathr;

use Mathr\Node\NullNode;
use Mathr\Node\NumberNode;
use Mathr\Node\FunctionNode;
use Mathr\Node\OperatorNode;
use Mathr\Node\VariableNode;
use Mathr\Node\NodeInterface;

abstract class Node implements NodeInterface
{
	/**
	 * Node's internal value.
	 * @var mixed Value held by node.
	 */
	protected $value;
	
	/**
	 * Node constructor.
	 * @param mixed $value Value to be held by this node.
	 */
	public function __construct($value)
	{
		$this->value = $value;
	}
	
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
	final public function getValue()
	{
		return $this->value;
	}
	
	/**
	 * @inheritdoc
	 */
	abstract public function evaluate(Scope $scope): Node;
	
	/**
	 * Creates a node based in a token.
	 * @param Token $token Token from which node is built from.
	 * @param \SplStack $stack Input stack.
	 * @return Node Created node.
	 */
	public static function fromToken(Token $token, \SplStack $stack): Node
	{
		if($token->is(Token::NUMBER))
			return NumberNode::fromToken($token, $stack);
		
		if($token->is(Token::VARIABLE))
			return VariableNode::fromToken($token, $stack);
		
		if($token->is(Token::FUNCTION))
			return FunctionNode::fromToken($token, $stack);
		
		if($token->is(Token::OPERATOR))
			return OperatorNode::fromToken($token, $stack);
		
		return new NullNode;
	}
	
}

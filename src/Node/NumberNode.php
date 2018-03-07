<?php
/**
 * Mathr\Node\NumberNode class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017-2018 Rodrigo Siqueira
 */
namespace Mathr\Node;

use Mathr\Node;
use Mathr\Scope;
use Mathr\Token;
use Mathr\Exception\NodeException;

class NumberNode extends Node
{
	/**
	 * NumberNode constructor.
	 * @param mixed $value Value held by Node.
	 * @throws NodeException
	 */
	public function __construct($value)
	{
		if(!is_numeric($value))
			throw NodeException::invalidNumber($value);
		
		parent::__construct($value);
	}
	
	/**
	 * @inheritdoc
	 */
	public function evaluate(Scope $scope): Node
	{
		return $this;
	}
	
	/**
	 * Creates a new NumberNode from a string numeric representation.
	 * @param string $data String to be instantiated as NumberNode.
	 * @return Node New created node.
	 */
	public static function fromString(string $data) : Node
	{
		return new static(
			strpos($data, '.') === false ? intval($data) : doubleval($data)
		);
	}
	
	/**
	 * @inheritdoc
	 */
	public static function fromToken(Token $token, \SplStack $stack): Node
	{
		return static::fromString($token->getData());
	}
	
}

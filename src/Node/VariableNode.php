<?php
/**
 * Mathr\Node\VariableNode class file.
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

class VariableNode extends Node
{
	/**
	 * {@inheritdoc}
	 */
	public function evaluate(Scope $scope): Node
	{
		$value = $scope->getVariable($this);
		
		if($value instanceof NullNode)
			throw NodeException::unknownSymbol($this->value);
		
		return $value->evaluate($scope);
	}
	
	/**
	 * Changes this variable name to a stack frame parameter.
	 * @param int $sequence Stack offset, new variable name.
	 */
	public function mapTo(int $sequence)
	{
		$this->value = sprintf('$%d', $sequence);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function compress(): string
	{
		return $this->getValue().":".Token::VARIABLE;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public static function fromToken(Token $token, \SplStack $stack): Node
	{
		return new static($token->getData());
	}
}

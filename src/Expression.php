<?php
/**
 * Mathr\Expression class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017-2018 Rodrigo Siqueira
 */
namespace Mathr;

class Expression extends \SplQueue
{
	/**
	 * Evaluates the expression and returns a value.
	 * @param Scope $scope Scope containing variables and functions.
	 * @param \SplStack $stack The evaluation stack.
	 * @return Token The evaluated expression result.
	 */
	public function evaluate(Scope $scope, \SplStack $stack): Token
	{
		while(!$this->isEmpty()) {
			$token = $this->shift();
			
			if($token->is(Token::NUMBER))
				$stack->push(NumberNode::fromToken($token, $stack));
			
			elseif($token->is(Token::VARIABLE))
				$stack->push(VariableNode::fromToken($token, $stack));
			
			elseif($token->is(Token::FUNCTION))
				$stack->push(FunctionNode::fromToken($token, $stack));
			
			elseif($token->is(Token::OPERATOR))
				$stack->push(OperatorNode::fromToken($token, $stack));
		}
		
		$stack->pop()->evaluate($scope);
	}
	
	/**
	 * Serializes the expression for storage as a string.
	 * @return string Encrypted expression.
	 */
	public function encrypt(): string
	{
		$serialized = $this->count();
		
		foreach($this as $token)
			$serialized .= ';'.$token->getType().':'.$token->getData();
		
		return $serialized;
	}
	
	/**
	 * Unserializes a string and creates a new expression.
	 * @param string $expr The string to be unserialized.
	 * @return Expression The unserialized expression.
	 */
	public static function decrypt(string $expr): self
	{
		$token = explode(';', $expr);
		$count = (int)$token[0];
		$queue = new self;
		
		for($i = 1; $i <= $count; ++$i) {
			list($type, $data) = explode(':', $token[$i]);
			$queue->push(new Token($data, (int)$type, -1));
		}
		
		return $queue;
	}
}

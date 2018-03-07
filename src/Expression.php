<?php
/**
 * Mathr\Expression class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017-2018 Rodrigo Siqueira
 */
namespace Mathr;

use Mathr\Node\NodeInterface;

class Expression extends \SplQueue implements NodeInterface
{
	/**
	 * Evaluates the expression and returns a value.
	 * @param Scope $scope Scope containing variables and functions.
	 * @return Node The evaluated expression result.
	 */
	public function evaluate(Scope $scope): Node
	{
		$stack = new \SplStack;
		
		for($i = 0; $i < $this->count(); ++$i)
			$stack->push(Node::fromToken($this[$i], $stack));

		return $stack->pop()->evaluate($scope);
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

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
	 * Builds an execution tree for the expression.
	 * @return Node The execution tree root.
	 */
	public function build(): Node
	{
		$stack = new \SplStack;
		
		for($i = 0; $i < $this->count(); ++$i)
			$stack->push(Node::fromToken($this[$i], $stack));
		
		return $stack->pop();
	}
	
	/**
	 * Evaluates the expression and returns a value.
	 * @param Scope $scope Scope containing variables and functions.
	 * @return Node The evaluated expression result.
	 */
	public function evaluate(Scope $scope): Node
	{
		return $this->build()->evaluate($scope);
	}
	
	/**
	 * Transforms the expression back into a string.
	 * @return string The node represented as a string.
	 */
	public function compress(): string
	{
		return $this->build()->compress();
	}
	
	/**
	 * Uncompresses and builds the expression from a string.
	 * @param string $data Data to be uncompressed.
	 * @return Node The built expression.
	 */
	public static function uncompress(string $data): Node
	{
		$tokens = explode(";", $data);
		$queue = new self;
		
		foreach($tokens as $token) {
			list($data, $type) = explode(":", $token);
			$queue->push(new Token($data, (int)$type, -1));
		}
		
		return $queue->build();
	}
}

<?php
/**
 * Mathr\Tree class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr;

use SplStack as Stack;
use SplQueue as Queue;
use Mathr\Parser\Token;
use Mathr\Node\NumberNode;
use Mathr\Node\VariableNode;
use Mathr\Node\OperatorNode;
use Mathr\Node\FunctionDeclNode;
use Mathr\Node\NativeFunctionNode;
use Mathr\Node\NativeVariableNode;

class Tree
{
	/**
	 * Tree root.
	 * @var Node\AbstractNode Tree's root node.
	 */
	protected $root;
	
	/**
	 * Tree constructor.
	 * @param Queue $queue Expression tokens in RPN format.
	 */
	public function __construct(Queue $queue)
	{
		$stack = new Stack;
		
		while(!$queue->isEmpty()) {
			$token = $queue->shift();
			
			if($token->is(Token::NUMBER)) {
				$stack->push(NumberNode::fromToken($token, $stack));
				continue;
			}
			
			if($token->is(Token::VARIABLE)) {
				$stack->push(in_array($token->data(), NativeVariableNode::LIST)
					? NativeVariableNode::fromToken($token, $stack)
					: VariableNode::fromToken($token, $stack)
				);
				continue;
			}
			
			if($token->is(Token::FUNCTION)) {
				$stack->push(in_array($token->data(), NativeFunctionNode::LIST)
					? NativeFunctionNode::fromToken($token, $stack)
					: FunctionDeclNode::fromToken($token, $stack)
				);
				continue;
			}
			
			if($token->is(Token::OPERATOR)) {
				$stack->push(OperatorNode::fromToken($token, $stack));
				continue;
			}
		}
		
		$this->root = $stack->pop();
	}
	
	/**
	 * Evaluates the expression tree and returns a value.
	 * @param Scope $scope Storage for variables and functions.
	 * @return Node\AbstractNode Resulting node after evaluation.
	 */
	public function evaluate(Scope $scope)
	{
		return $this->root->evaluate($scope);
	}
	
}

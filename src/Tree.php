<?php
/**
 * Mathr\Tree class file.
 * @package Parser
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

class Tree
{
	/**
	 * @var Node\AbstractNode
	 */
	protected $root;
	
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
				$stack->push(VariableNode::fromToken($token, $stack));
				continue;
			}
			
			if($token->is(Token::FUNCTION)) {
				$stack->push(FunctionDeclNode::fromToken($token, $stack));
				continue;
			}
			
			if($token->is(Token::OPERATOR)) {
				$stack->push(OperatorNode::fromToken($token, $stack));
				continue;
			}
		}
		
		$this->root = $stack->pop();
	}
	
	public function evaluate(Scope $scope)
	{
		return $this->root->evaluate($scope);
	}
	
}

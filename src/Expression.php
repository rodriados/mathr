<?php
/**
 * Mathr\Expression class file.
 * @package Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr;

use SplStack;
use SplQueue;
use Mathr\Parser\Token;
use Mathr\Expression\Node\NumberNode;
use Mathr\Expression\Node\VariableNode;
use Mathr\Expression\Node\OperatorNode;
use Mathr\Expression\Node\FunctionNode;

class Expression
{
	protected $root;
	private $stack;
	
	public function __construct(SplQueue $queue)
	{
		$this->stack = new SplStack;
		
		while(!$queue->isEmpty()) {
			$token = $queue->shift();
			
			if($token->is(Token::NUMBER)) {
				$this->stack->push(new NumberNode($token));
				continue;
			}
			
			if($token->is(Token::VARIABLE)) {
				$this->stack->push(new VariableNode($token));
				continue;
			}
			
			if($token->is(Token::FUNCTION)) {
				$this->stack->push($this->buildFunction($token));
				continue;
			}
			
			if($token->is(Token::OPERATOR)) {
				$this->stack->push($this->buildOperator($token));
				continue;
			}
		}
		
		$this->root = $this->stack->pop();
	}
	
	public function evaluate(Scope $scope)
	{
		return $this->root->evaluate($scope);
	}
	
	private function buildFunction(Token $token) : FunctionNode
	{
		$node = new FunctionNode($token, $this->stack);
		return $node;
	}
	
	private function buildOperator(Token $token) : OperatorNode
	{
		$node = new OperatorNode($token, $this->stack);
		return $node;
	}
	
}

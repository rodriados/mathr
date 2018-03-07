<?php
/**
 * Mathr\Node\FunctionNode class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017-2018 Rodrigo Siqueira
 */
namespace Mathr\Node;

use Mathr\Exception\NodeException;
use Mathr\Node;
use Mathr\Scope;
use Mathr\Native;

class FunctionNode extends OperatorNode
{
	/**
	 * FunctionNode constructor.
	 * @param string $symbol The operator symbol.
	 * @param \SplStack $stack The operands' stack.
	 */
	public function __construct(string $symbol, \SplStack $stack)
	{
		$this->argc = (int)$stack->pop()->getValue();
		parent::__construct($symbol, $stack);
	}
	
	/**
	 * @inheritdoc
	 */
	public function evaluate(Scope $scope): Node
	{
		$func = $scope->getFunction($this);
		
		if($func instanceof NullNode)
			throw NodeException::unknownSymbol($this->value);
		
		$argv = clone $this->argv;
		$isNum = true;
		
		foreach($argv as $id => $arg) {
			$argv[$id] = $arg->evaluate($scope);
			$isNum = $isNum && $argv[$id] instanceof NumberNode;
		}
		
		if($func instanceof OperatorNode)
			return $isNum
				? Native::{$this->value}(...$argv)
				: $this;
		
		$heap = new \SplPriorityQueue;
		
		/**
		 * @var FunctionNode $decl
		 * @var Node $block
		 */
		foreach($func as $i => list($decl, $block)) {
			if($this->argc != $decl->argc)
				continue;
			
			$priority = 0;
			
			foreach($decl->argv as $j => $arg)
				if($arg instanceof NumberNode && $arg->getValue() == $argv[$j]->getValue())
					++$priority;
				elseif(!$arg instanceof VariableNode)
					continue 2;
				
			$heap->insert($block, [$priority, $i]);
		}
		
		if($heap->isEmpty())
			throw NodeException::incompatibleDefinition($this->value);
		
		$scope->push($argv->toArray());
		$value = $heap->top()->evaluate($scope);
		$scope->pop();
		
		return $value;
	}
	
	/**
	 * Substitutes parameters for stack frame variables.
	 * @param Node $body Function execution body.
	 * @return self The processed function node.
	 */
	public function processBody(Node $body): self
	{
		$stack = new \SplStack;
		$stack->push($body);
		$map = [];
		
		foreach($this->argv as $id => $arg)
			if($arg instanceof VariableNode)
				$map[$arg->getValue()] = $id + 1;
		
		while(!$stack->isEmpty()) {
			$node = $stack->shift();
			
			if($node instanceof VariableNode && isset($map[$node->getValue()]))
				$node->mapTo($map[$node->getValue()]);
			
			elseif($node instanceof OperatorNode)
				foreach($node->argv as $arg)
					$stack->push($arg);
		}
		
		return $this;
	}
}

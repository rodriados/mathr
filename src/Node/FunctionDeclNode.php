<?php
/**
 * Mathr\Node\FunctionDeclNode class file.
 * @package Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr\Node;

use SplStack;
use SplPriorityQueue;
use Mathr\Scope;
use Mathr\Parser\Token;
use Mathr\Exception\UnknownSymbolException;
use Mathr\Exception\IncorrectFunctionParametersException;
use Mathr\Exception\NoCompatibleDefinitionFoundException;

class FunctionDeclNode extends OperatorNode
{
	/**
	 * Represents this node as a string.
	 * @return string Node's string representation.
	 */
	public function __toString()
	{
		$str = null;
		
		for($i = 0; $i < $this->argc; ++$i)
			$str .= $this->argv[$i].' ';
		
		return $str.$this->argc.' '.$this->value;
	}
	
	/**
	 * Substitutes parameters for stack frame variables.
	 * @param AbstractNode $body Function execution body.
	 */
	public function processBody(AbstractNode $body)
	{
		$stack = new SplStack;
		$stack->push($body);
		$map = [];
		
		foreach($this->argv as $id => $arg)
			if($arg instanceof VariableNode)
				$map[$arg->value()] = $id + 1;
		
		while(!$stack->isEmpty()) {
			$node = $stack->shift();
			
			if($node instanceof VariableNode && array_key_exists($node->value(), $map))
				$node->mapToParam($map[$node->value()]);
			
			elseif($node instanceof OperatorNode)
				foreach($node->argv as $arg)
					$stack->push($arg);
		}
		
	}

	/**
	 * @inheritdoc
	 * @throws UnknownSymbolException
	 * @throws IncorrectFunctionParametersException
	 * @throws NoCompatibleDefinitionFoundException
	 */
	public function evaluate(Scope $scope) : AbstractNode
	{
		$heap = new SplPriorityQueue;
		$argv = [];
		
		if(!is_array($funcs = $scope->retrieve($this)))
			throw new UnknownSymbolException($this->value());
		
		foreach($this->argv as $arg)
			$argv[] = $arg->evaluate($scope);
		
		/**
		 * @var FunctionDeclNode $decl
		 * @var AbstractNode $block
		 */
		foreach($funcs as $i => list($decl, $block)) {
			if($this->argc != $decl->argc)
				continue;
			
			$priority = 0;
			
			foreach($decl->argv as $j => $arg)
				if($arg instanceof NumberNode && $arg->value() == $argv[$j]->value())
					++$priority;
				elseif(!$arg instanceof VariableNode)
					continue 2;
				
			$heap->insert($block, [$priority, $i]);
		}
		
		if($heap->isEmpty())
			throw new NoCompatibleDefinitionFoundException($this->value());
		
		$scope->push($argv ?? []);
		$value = $heap->top()->evaluate($scope);
		$scope->pop();
		
		return $value;
	}
	
	/**
	 * @inheritdoc
	 */
	public static function fromToken(Token $token, SplStack $stack) : AbstractNode
	{
		return new static($token->data(), $stack);
	}
}

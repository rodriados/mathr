<?php
/**
 * Mathr\Expression\Node\FunctionNode class file.
 * @package Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr\Expression\Node;

use Mathr\Scope;
use SplStack;
use Mathr\Parser\Token;
use Mathr\Expression\Node;
use Mathr\Exception\UnknownSymbolException;
use Mathr\Exception\IncorrectFunctionParametersException;

class FunctionNode
	extends Node
{
	protected $count;
	protected $children = [];
	
	public function __construct(Token $token, SplStack $stack)
	{
		parent::__construct($token);
		$this->count = $stack->pop()->value();
		$this->children = [];
		
		for($i = 0; $i < $this->count; ++$i)
			array_unshift($this->children, $stack->pop());
	}
	
	/**
	 * @param Scope $scope
	 * @return Node
	 * @throws UnknownSymbolException
	 * @throws IncorrectFunctionParametersException
	 */
	public function evaluate(Scope $scope) : Node
	{
		foreach($this->children as &$child)
			$child = $child->evaluate($scope);
		
		if(!$scope->has($this->value()))
			throw new UnknownSymbolException($this->value());
		/**
		 * @var FunctionNode $vars
		 * @var Node $target
		 */
		list($vars, $target) = $scope->retrieve($this->value());
		
		if(!$vars instanceof FunctionNode)
			throw new UnknownSymbolException($this->value());
		
		if($vars->count != $this->count)
			throw new IncorrectFunctionParametersException($this->value());
		
		$innerScope = new Scope($scope);
		
		foreach($this->children as $id => $child)
			$innerScope->assign(
				$vars->children[$id]->value(),
				[$vars->children[$id], $child]
			);
		
		return $target->evaluate($innerScope);
	}
	
}

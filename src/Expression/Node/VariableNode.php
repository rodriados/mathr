<?php
/**
 * Mathr\Expression\Node\VariableNode class file.
 * @package Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr\Expression\Node;

use Mathr\Scope;
use Mathr\Expression\Node;
use Mathr\Exception\UnknownSymbolException;

class VariableNode
	extends Node
{
	/**
	 * @param Scope $scope
	 * @return Node
	 * @throws UnknownSymbolException
	 */
	public function evaluate(Scope $scope) : Node
	{
		if(!$scope->has($this->value()))
			throw new UnknownSymbolException($this->value());
		/**
		 * @var VariableNode $var
		 * @var Node $target
		 */
		list($var, $target) = $scope->retrieve($this->value());
		
		if(!$var instanceof VariableNode)
			throw new UnknownSymbolException($this->value());
		
		return $target->evaluate($scope);
	}
	
}

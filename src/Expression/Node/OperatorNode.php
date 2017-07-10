<?php
/**
 * Mathr\Expression\Node\OperatorNode class file.
 * @package Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr\Expression\Node;

use SplStack;
use Mathr\Scope;
use Mathr\Parser\Token;
use Mathr\Expression\Node;

class OperatorNode
	extends Node
{
	protected static $count = [
		'0-'    => 1,
	    '0+'    => 1,
	    '+'     => 2,
	    '-'     => 2,
		'*'     => 2,
	    '/'     => 2,
	    '^'     => 2,
	    '='     => 2
	];
	
	protected $children;
	
	public function __construct(Token $token, SplStack $stack)
	{
		parent::__construct($token);
		$this->children = [];
		
		for($i = 0; $i < self::$count[$token->data()]; ++$i)
			array_unshift($this->children, $stack->pop());
	}
	
	/**
	 * @param Scope $scope
	 * @return mixed
	 */
	public function evaluate(Scope $scope) : Node
	{
		if($this->value() != '=')
			foreach($this->children as &$child)
				$child = $child->evaluate($scope);
		
		if(
			$this->value() == '0-' &&
			$this->children[0] instanceof NumberNode
		)
			return $this->children[0]->transform(function ($val) {
				return -$val;
			});
		
		if(
			$this->value() == '0+' &&
			$this->children[0] instanceof NumberNode
		)
			return $this->children[0]->transform(function ($val) {
				return +$val;
			});

		if(
			$this->value() == '+' &&
		    $this->children[0] instanceof NumberNode &&
		    $this->children[1] instanceof NumberNode
		)
			return $this->children[0]->transform(function ($val) {
				return $val + $this->children[1]->value();
			});
		
		if(
			$this->value() == '-' &&
			$this->children[0] instanceof NumberNode &&
			$this->children[1] instanceof NumberNode
		)
			return $this->children[0]->transform(function ($val) {
				return $val - $this->children[1]->value();
			});
		
		if(
			$this->value() == '*' &&
			$this->children[0] instanceof NumberNode &&
			$this->children[1] instanceof NumberNode
		)
			return $this->children[0]->transform(function ($val) {
				return $val * $this->children[1]->value();
			});
		
		if(
			$this->value() == '/' &&
			$this->children[0] instanceof NumberNode &&
			$this->children[1] instanceof NumberNode
		)
			return $this->children[0]->transform(function ($val) {
				return $val / $this->children[1]->value();
			});
		
		if(
			$this->value() == '^' &&
			$this->children[0] instanceof NumberNode &&
			$this->children[1] instanceof NumberNode
		)
			return $this->children[0]->transform(function ($val) {
				return pow($val, $this->children[1]->value());
			});
		
		if(
			$this->value() == '=' &&
			($this->children[0] instanceof FunctionNode ||
			$this->children[0] instanceof VariableNode)
		) {
			$scope->assign($this->children[0]->value(), $this->children);
			return $this->children[1];
		}
		
		return $this;
	}
	
}

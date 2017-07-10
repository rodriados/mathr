<?php
/**
 * Mathr\Expression\Node\NumberNode class file.
 * @package Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr\Expression\Node;

use Mathr\Parser\Token;
use Mathr\Expression\Node;
use Mathr\Scope;

class NumberNode
	extends Node
{
	/**
	 * NumberNode constructor.
	 * @param mixed $token
	 */
	public function __construct(Token $token)
	{
		parent::__construct($token);
		$this->value = strpos($this->value, '.') === false
			? intval($this->value)
			: doubleval($this->value);
	}
	
	/**
	 * @param Scope $scope
	 * @return Node
	 */
	public function evaluate(Scope $scope) : Node
	{
		return $this;
	}
	
	public function transform(callable $fn) : Node
	{
		$copy = clone $this;
		$copy->value = $fn($this->value);
		return $copy;
	}
	
}

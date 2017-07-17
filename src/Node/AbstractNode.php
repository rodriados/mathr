<?php
/**
 * Mathr\Node\AbstractNode class file.
 * @package Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr\Node;

use Mathr\Scope;
use Mathr\Parser\Token;
use SplStack as Stack;

abstract class AbstractNode
{
	protected $value;
	
	public function __toString()
	{
		return (string)($this->value);
	}
	
	/**
	 * @return mixed
	 */
	public function value()
	{
		return $this->value;
	}
	
	abstract public function evaluate(Scope $scope) : AbstractNode;
	abstract public static function fromToken(Token $token, Stack $stack) : AbstractNode;
	
}

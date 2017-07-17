<?php
/**
 * Mathr\Node\NullNode class file.
 * @package Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr\Node;

use SplStack;
use Mathr\Scope;
use Mathr\Parser\Token;

class NullNode
	extends AbstractNode
{
	public function __construct()
	{
		$this->value = null;
	}
	
	/**
	 * @param Scope $scope
	 * @return AbstractNode
	 */
	public function evaluate(Scope $scope) : AbstractNode
	{
		return $this;
	}
	
	public static function fromToken(Token $token, SplStack $stack) : AbstractNode
	{
		return new static;
	}
	
}

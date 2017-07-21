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
	/**
	 * NullNode constructor.
	 */
	public function __construct()
	{
		$this->value = null;
	}
	
	/**
	 * @inheritdoc
	 */
	public function evaluate(Scope $scope) : AbstractNode
	{
		return $this;
	}
	
	/**
	 * @inheritdoc
	 */
	public static function fromToken(Token $token, SplStack $stack) : AbstractNode
	{
		return new static;
	}
	
}

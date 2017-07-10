<?php
/**
 * Mathr\Expression\Node class file.
 * @package Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr\Expression;

use Mathr\Scope;
use Mathr\Parser\Token;

abstract class Node
{
	protected $value;
	
	public function __construct(Token $token)
	{
		$this->value = $token->data();
	}
	
	/**
	 * @return mixed
	 */
	public function value()
	{
		return $this->value;
	}
	
	abstract public function evaluate(Scope $scope) : Node;
	
}

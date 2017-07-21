<?php
/**
 * Mathr\Node\VariableNode class file.
 * @package Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr\Node;

use SplStack;
use Mathr\Scope;
use Mathr\Parser\Token;
use Mathr\Exception\UnknownSymbolException;

class VariableNode
	extends AbstractNode
{
	/**
	 * VariableNode constructor.
	 * @param string $name Name of the invoked variable.
	 */
	public function __construct(string $name)
	{
		$this->value = $name;
	}
	
	/**
	 * Changes this variable name to a stack frame parameter name.
	 * @param int $sequence Stack offset, new variable name.
	 */
	public function mapToParam(int $sequence)
	{
		$this->value = "\${$sequence}";
	}
	
	/**
	 * @inheritdoc
	 */
	public function evaluate(Scope $scope) : AbstractNode
	{
		if(!($target = $scope->retrieve($this)))
			throw new UnknownSymbolException($this->value());
		
		return $target->evaluate($scope);
	}
	
	/**
	 * @inheritdoc
	 */
	public static function fromToken(Token $token, SplStack $_) : AbstractNode
	{
		return new static($token->data());
	}
}

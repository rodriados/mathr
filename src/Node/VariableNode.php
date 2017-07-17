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
	public function __construct(string $name)
	{
		$this->value = $name;
	}
	
	public function mapToParam(int $sequence)
	{
		$this->value = "\${$sequence}";
	}
	
	/**
	 * @param Scope $scope
	 * @return AbstractNode
	 * @throws UnknownSymbolException
	 */
	public function evaluate(Scope $scope) : AbstractNode
	{
		if(!($target = $scope->retrieve($this)))
			throw new UnknownSymbolException($this->value());
		
		return $target->evaluate($scope);
	}
	
	/**
	 * @param Token $token
	 * @param SplStack $_
	 * @return AbstractNode
	 */
	public static function fromToken(Token $token, SplStack $_) : AbstractNode
	{
		return new static($token->data());
	}
}

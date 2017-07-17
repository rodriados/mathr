<?php
/**
 * Mathr\Node\NativeVariableNode class file.
 * @package Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr\Node;

use SplStack;
use Mathr\Scope;
use Mathr\Parser\Token;

class NativeVariableNode
	extends AbstractNode
{
	const LIST = [
		'$e', '$inf', '$pi', '$π', '$phi', '$φ', '$psi', '$ψ'
	];
	
	public function __construct(string $value, SplStack $stack)
	{
		$this->value = $value;
	}
	
	/**
	 * @param Scope $scope
	 * @return AbstractNode
	 */
	public function evaluate(Scope $scope) : AbstractNode
	{
		static $constants;
		
		if(!isset($constants))
			$constants = [
				'$e'    => M_E,
			    '$inf'  => INF,
				'$pi'   => M_PI,
				'$π'    => M_PI,
				'$phi'  => 1.618033988749894,
				'$φ'    => 1.618033988749894,
				'$psi'  => 3.359885666243177,
				'$ψ'    => 3.359885666243177,
			];
		
		return new NumberNode($constants[$this->value()]);
	}
	
	/**
	 * @param Token $token
	 * @param SplStack $stack
	 * @return AbstractNode
	 */
	public static function fromToken(Token $token, SplStack $stack) : AbstractNode
	{
		return new static($token->data(), $stack);
	}
	
}

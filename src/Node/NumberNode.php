<?php
/**
 * Mathr\Node\NumberNode class file.
 * @package Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr\Node;

use SplStack;
use Exception;
use Mathr\Scope;
use Mathr\Parser\Token;

class NumberNode
	extends AbstractNode
{
	/**
	 * NumberNode constructor.
	 * @param float|int $value Value held by Node.
	 * @throws Exception
	 */
	public function __construct($value)
	{
		if(!is_numeric($value))
			throw new Exception('NumberNode received a non-numeric value');
			
		$this->value = $value;
	}
	
	/**
	 * @inheritdoc
	 */
	public function evaluate(Scope $scope) : AbstractNode
	{
		return $this;
	}
	
	/**
	 * Creates a new NumberNode from a string numeric representation.
	 * @param string $data String to be instantiated as NumberNode.
	 * @return AbstractNode New created node.
	 */
	public static function fromString(string $data) : AbstractNode
	{
		return new static(
			strpos($data, '.') === false
				? intval($data)
				: doubleval($data)
		);
	}
	
	/**
	 * @inheritdoc
	 */
	public static function fromToken(Token $token, SplStack $stack) : AbstractNode
	{
		return static::fromString($token->data());
	}
	
}

<?php
/**
 * Mathr\Node\OperatorNode class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017-2018 Rodrigo Siqueira
 */
namespace Mathr\Node;

use Mathr\Node;
use Mathr\Scope;
use Mathr\Token;
use Mathr\Native;

class OperatorNode extends Node
{
	/**
	 * Informs how many operands each operator need.
	 * @var int[] Operators' operands count.
	 */
	const OP_ARGC = [
		'0+' => 1,
		'0-' => 1,
		'+'  => 2,
		'-'  => 2,
		'*'  => 2,
		'/'  => 2,
		'^'  => 2,
		'='  => 2,
	];
	
	/**
	 * Informs the operators' names.
	 * @var string[] Operators' names.
	 */
	const OP_NAME = [
		'0+' => '_pos_',
		'0-' => '_neg_',
		'+'  => '_sum_',
		'-'  => '_sub_',
		'*'  => '_mul_',
		'/'  => '_div_',
		'^'  => '_pow_',
		'='  => '=',
	];
	
	/**
	 * Operator operands.
	 * @var \SplFixedArray Operands storage.
	 */
	protected $argv;
	
	/**
	 * Operator operands count.
	 * @var int Number of registered operands.
	 */
	protected $argc = 0;
	
	/**
	 * OperatorNode constructor.
	 * @param string $symbol The operator symbol.
	 * @param \SplStack $stack The operands' stack.
	 */
	public function __construct(string $symbol, \SplStack $stack = null)
	{
		$this->argc = self::OP_ARGC[$symbol] ?? $this->argc;
		$this->argv = new \SplFixedArray($this->argc);
		
		for($i = $this->argc - 1; $i >= 0; --$i)
			$this->argv[$i] = $stack->pop();

		parent::__construct(self::OP_NAME[$symbol] ?? $symbol);
	}
	
	/**
	 * @inheritdoc
	 */
	public function evaluate(Scope $scope): Node
	{
		$argv = clone $this->argv;
		$isNum = true;
		
		if($this->value != '=') {
			foreach($argv as $id => $arg) {
				$argv[$id] = $arg->evaluate($scope);
				$isNum = $isNum && $argv[$id] instanceof NumberNode;
			}
			
			return $isNum
				? Native::{$this->value}(...$argv)
				: $this;
		}
		
		if($argv[1]->getValue() == '=')
			$argv[1] = $argv[1]->evaluate($scope);
		
		$argv[0] instanceof FunctionNode
			? $scope->setFunction($argv[0]->processBody($argv[1]), $argv[1])
			: $scope->setVariable($argv[0], $argv[1]);
		
		return $argv[1];
	}
	
	/**
	 * @inheritdoc
	 */
	public static function fromToken(Token $token, \SplStack $stack): Node
	{
		return new static($token->getData(), $stack);
	}
}

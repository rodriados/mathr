<?php
/**
 * Mathr\Node\OperatorNode class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr\Node;

use SplStack;
use SplFixedArray;
use Mathr\Scope;
use Mathr\Parser\Token;

class OperatorNode
	extends AbstractNode
{
	const POS = '0+';
	const NEG = '0-';
	const SUM = '+';
	const SUB = '-';
	const MUL = '*';
	const DIV = '/';
	const POW = '^';
	const EQU = '=';
	
	/**
	 * Informs how many operands each operator need.
	 * @var int[] Operators' operands count.
	 */
	protected static $ops = [
		self::POS => 1,
	    self::NEG => 1,
	    self::SUM => 2,
	    self::SUB => 2,
		self::MUL => 2,
	    self::DIV => 2,
	    self::POW => 2,
	    self::EQU => 2
	];
	
	/**
	 * Operator operands.
	 * @var SplFixedArray Operands storage.
	 */
	protected $argv;
	
	/**
	 * Operator operands count.
	 * @var int Number of registered operands.
	 */
	protected $argc;
	
	/**
	 * OperatorNode constructor.
	 * @param string $value Operator name.
	 * @param SplStack $stack Operands stack.
	 */
	public function __construct(string $value, SplStack $stack)
	{
		$this->value = $value;
		$this->argc = !array_key_exists($value, self::$ops)
			? (int)$stack->pop()->value()
			: self::$ops[$value];
		$this->argv = new SplFixedArray($this->argc);
		
		for($i = $this->argc - 1; $i >= 0; --$i)
			$this->argv[$i] = $stack->pop();
	}
	
	/**
	 * Represents this node as a string.
	 * @return string Node's string representation.
	 */
	public function __toString()
	{
		$str = null;
		
		for($i = 0; $i < $this->argc; ++$i)
			$str .= $this->argv[$i].' ';
		
		return $str.$this->value;
	}
	
	/**
	 * @inheritdoc
	 */
	public function evaluate(Scope $scope) : AbstractNode
	{
		$argv = clone $this->argv;
		
		if($this->value() != self::EQU)
			foreach($argv as $id => $arg)
				$argv[$id] = $arg->evaluate($scope);
		
		if(
			$this->value() == self::NEG &&
			$argv[0] instanceof NumberNode
		)
			return new NumberNode(
				-$argv[0]->value()
			);
		
		if(
			$this->value() == self::POS &&
			$argv[0] instanceof NumberNode
		)
			return new NumberNode(
				+$argv[0]->value()
			);
		
		if(
			$this->value() == '+' &&
			$argv[0] instanceof NumberNode &&
			$argv[1] instanceof NumberNode
		)
			return new NumberNode(
				$argv[0]->value() + $argv[1]->value()
			);
		
		if(
			$this->value() == '-' &&
			$argv[0] instanceof NumberNode &&
			$argv[1] instanceof NumberNode
		)
			return new NumberNode(
				$argv[0]->value() - $argv[1]->value()
			);
		
		if(
			$this->value() == '*' &&
			$argv[0] instanceof NumberNode &&
			$argv[1] instanceof NumberNode
		)
			return new NumberNode(
				$argv[0]->value() * $argv[1]->value()
			);
		
		if(
			$this->value() == '/' &&
			$argv[0] instanceof NumberNode &&
			$argv[1] instanceof NumberNode
		)
			return new NumberNode(
				$argv[0]->value() / $argv[1]->value()
			);
		
		if(
			$this->value() == '^' &&
			$argv[0] instanceof NumberNode &&
			$argv[1] instanceof NumberNode
		)
			return new NumberNode(
				pow($argv[0]->value(), $argv[1]->value())
			);

		if(
			$this->value() == '=' &&
		    $argv[0] instanceof VariableNode
		) {
			if($argv[1]->value() == '=')
				$argv[1] = $argv[1]->evaluate($scope);
			
			$scope->assign(...$argv);
			return $argv[1];
		}
		
		if(
			$this->value() == '=' &&
			$argv[0] instanceof FunctionDeclNode
		) {
			if($argv[1]->value() == '=')
				$argv[1] = $argv[1]->evaluate($scope);
			
			$argv[0]->processBody($argv[1]);
			$scope->assign(...$argv);
			return $argv[1];
		}
		
		return $this;
	}
	
	/**
	 * @inheritdoc
	 */
	public static function fromToken(Token $token, SplStack $stack) : AbstractNode
	{
		return new static($token->data(), $stack);
	}
}

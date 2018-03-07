<?php
/**
 * Mathr\Scope class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017-2018 Rodrigo Siqueira
 */
namespace Mathr;

use Mathr\Exception\ScopeException;

class Scope
{
	/**
	 * Informs the names of native constants.
	 * @var array
	 */
	const CONSTANT_LIST = [
		'$e', '$inf', '$pi', '$π', '$phi', '$φ', '$psi', '$ψ',
	];
	
	/**
	 * Informs the values of native constants.
	 * @var array
	 */
	const CONSTANT = [
		'$e'    => M_E,
		'$inf'  => INF,
		'$pi'   => M_PI,
		'$π'    => M_PI,
		'$phi'  => 1.618033988749894,
		'$φ'    => 1.618033988749894,
		'$psi'  => 3.359885666243177,
		'$ψ'    => 3.359885666243177,
	];
	
	/**
	 * Informs the names of native functions.
	 * @var array
	 */
	const FUNCTION_LIST = [
		'abs', 'acos', 'acosh', 'asin', 'asinh', 'atan', 'atanh', 'ceil',
		'cos', 'cosh', 'deg2rad', 'float', 'floor', 'hypot', 'int', 'log',
		'max', 'min', 'mod', 'rad2deg', 'rand', 'round', 'rt',
		'sin', 'sinh', 'sqrt', 'tan', 'tanh',
		'sum', 'sub', 'mult', 'div', 'pow', 'equ',
	];
	
	/**
	 * Informs the arguments' number of the native functions. If not in this
	 * list, the assumed number is 1. If negative, the function may accept more
	 * arguments. If positive, only the exact number of arguments is accepted.
	 * @var array
	 */
	const FUNCTION_ARGC = [
		'hypot' => +2,
		'log'   => -1,
		'max'   => -2,
		'min'   => -2,
		'mod'   => +2,
		'rand'  => 0,
		'round' => -1,
		'rt'    => +2,
	];
	
	/**
	 * List of declared variables.
	 * @var Token[] Stores all declared variables in scope.
	 */
	protected $var;
	
	/**
	 * List of declared functions.
	 * @var Expression[] Stores all declared functions in scope.
	 */
	protected $func;
	
	/**
	 * Execution stack.
	 * @var \SplStack Creation of stack frames while executing functions.
	 */
	protected $stack;
	
	/**
	 * Function calls depth.
	 * @var int Counts the current function calls depth.
	 */
	protected $depth;
	
	/**
	 * The limit for function calls depth.
	 * @var int Sets a rigid limit for function calls depth or recursion.
	 */
	protected $limit;
	
	/**
	 * Scope constructor.
	 * @param int $limit Limit for function calls depth.
	 */
	public function __construct(int $limit = 100)
	{
		$this->var = [];
		$this->func = [];
		
		$this->stack = new \SplStack;
		$this->limit = $limit;
		$this->depth = 0;
	}
	
	public function getVariable(Token $name): ?Token
	{
		$n = $name->getData();
		
		if(isset($this->var[$n]))
			return $this->var[$n];
		
		if(isset(self::CONSTANT_LIST[$n]))
			return self::CONSTANT[$n];
		
		return null;
	}
	
	public function getFunction(Token $name): Expression
	{
		$n = $name->getData();
		
		if(isset($this->func[$n]))
			return $this->func[$n];
		
		#if(isset(self::FUNCTION_LIST[$n]))
		#	return Function::native($n);
		
		throw ScopeException::unknownFunction($name);
	}
	
	public function setVariable(Token $name, Token $value)
	{
		$this->var[$name->getData()] = $value;
	}
	
	public function setFunction(Token $name, Expression $body)
	{
		$this->var[$name->getData()][] = $body;
	}
	
	public function pop()
	{
		if($this->stack->isEmpty())
			throw ScopeException::stackFrame();
		
		$count = $this->stack->pop();
		
		for($i = 0; $i < $count; ++$i)
			$this->stack->pop();
		
		--$this->depth;
	}
}

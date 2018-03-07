<?php
/**
 * Mathr\Native class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017-2018 Rodrigo Siqueira
 */
namespace Mathr;

use Mathr\Node\NumberNode;

class Native
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
		'pos', 'neg', 'sum', 'sub', 'mul', 'div', 'pow',
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
		'sum'   => +2,
		'sub'   => +2,
		'mul'   => +2,
		'div'   => +2,
		'pow'   => +2,
	];
	
	public static function pos(NumberNode $a): NumberNode
	{
		return $a;
	}
	
	public static function neg(NumberNode $a): NumberNode
	{
		return new NumberNode(-$a->getValue());
	}
	
	public static function sum(NumberNode $a, NumberNode $b): NumberNode
	{
		return new NumberNode($a->getValue() + $b->getValue());
	}
	
	public static function sub(NumberNode $a, NumberNode $b): NumberNode
	{
		return new NumberNode($a->getValue() - $b->getValue());
	}
	
	public static function mul(NumberNode $a, NumberNode $b): NumberNode
	{
		return new NumberNode($a->getValue() * $b->getValue());
	}
	
	public static function div(NumberNode $a, NumberNode $b): NumberNode
	{
		return new NumberNode($a->getValue() / $b->getValue());
	}
	
	public static function pow(NumberNode $a, NumberNode $b): NumberNode
	{
		return new NumberNode(pow($a->getValue(), $b->getValue()));
	}
}

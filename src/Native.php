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
		'_pos_', '_neg_', '_sum_', '_sub_', '_mul_', '_div_', '_pow_',
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
	
	public static function _pos_(NumberNode $a): NumberNode
	{
		return $a;
	}
	
	public static function _neg_(NumberNode $a): NumberNode
	{
		return new NumberNode(-$a->getValue());
	}
	
	public static function _sum_(NumberNode $a, NumberNode $b): NumberNode
	{
		return new NumberNode($a->getValue() + $b->getValue());
	}
	
	public static function _sub_(NumberNode $a, NumberNode $b): NumberNode
	{
		return new NumberNode($a->getValue() - $b->getValue());
	}
	
	public static function _mul_(NumberNode $a, NumberNode $b): NumberNode
	{
		return new NumberNode($a->getValue() * $b->getValue());
	}
	
	public static function _div_(NumberNode $a, NumberNode $b): NumberNode
	{
		return new NumberNode($a->getValue() / $b->getValue());
	}
	
	public static function _pow_(NumberNode $a, NumberNode $b): NumberNode
	{
		return new NumberNode(pow($a->getValue(), $b->getValue()));
	}

    public static function abs(NumberNode$v1): NumberNode
    {
		return new NumberNode(abs($v1->getValue()));
	}
	
    public static function acos(NumberNode$v1): NumberNode
    {
		return new NumberNode(acos($v1->getValue()));
    }
    
    public static function acosh(NumberNode$v1): NumberNode
    {
		return new NumberNode(acosh($v1->getValue()));
    }
    
    public static function asin(NumberNode$v1): NumberNode
    {
		return new NumberNode(asin($v1->getValue()));
    }
    
    public static function asinh(NumberNode$v1): NumberNode
    {
		return new NumberNode(asinh($v1->getValue()));
    }
    
    public static function atan(NumberNode$v1): NumberNode
    {
		return new NumberNode(atan($v1->getValue()));
    }
    
    public static function atanh(NumberNode$v1): NumberNode
    {
		return new NumberNode(atanh($v1->getValue()));
    }
    
    public static function ceil(NumberNode$v1): NumberNode
    {
		return new NumberNode(ceil($v1->getValue()));
    }
    
    public static function cos(NumberNode$v1): NumberNode
    {
		return new NumberNode(cos($v1->getValue()));
    }
    
    public static function cosh(NumberNode$v1): NumberNode
    {
		return new NumberNode(cosh($v1->getValue()));
    }
    
    public static function deg2rad(NumberNode$v1): NumberNode
    {
		return new NumberNode(deg2rad($v1->getValue()));
    }
    
    public static function float(NumberNode$v1): NumberNode
    {
		return new NumberNode((float)$v1->getValue());
    }
    
    public static function floor(NumberNode$v1): NumberNode
    {
		return new NumberNode(floor($v1->getValue()));
    }
    
    public static function hypot(NumberNode$v1, NumberNode $v2): NumberNode
    {
		return new NumberNode(hypot($v1->getValue(), $v2->getValue()));
    }
    
    public static function int(NumberNode$v1): NumberNode
    {
		return new NumberNode((int)$v1->getValue());
    }
    
    public static function log(NumberNode$v1, NumberNode $exp = null): NumberNode
    {
		return new NumberNode(log($v1->getValue(), $exp ? $exp->getValue() : M_E));
    }
    
    public static function max(NumberNode...$v): NumberNode
    {
    	$argv = [];
    	
	    foreach($v as $arg)
	    	$argv[] = $arg->getValue();
	    
		return new NumberNode(max(...$argv));
    }
    
    public static function min(NumberNode...$v): NumberNode
    {
	    $argv = [];
	
	    foreach($v as $arg)
		    $argv[] = $arg->getValue();
	
	    return new NumberNode(min(...$argv));
    }
    
    public static function mod(NumberNode$v1, NumberNode $v2): NumberNode
    {
    	$v1 = $v1->getValue();
	    $v2 = $v2->getValue();
    	
	    return new NumberNode(is_float($v1) || is_float($v2)
	        ? fmod($v1, $v2)
		    : $v1 % $v2
	    );
    }
    
    public static function rad2deg(NumberNode$v1): NumberNode
    {
		return new NumberNode(rad2deg($v1->getValue()));
    }
    
    public static function rand(): NumberNode
    {
		return new NumberNode(rand());
    }
	
	public static function round(NumberNode$v1, NumberNode $v2 = null): NumberNode
	{
		return new NumberNode(round($v1->getValue(), $v2 ? $v2->getValue() : 0));
    }
	
	public static function rt(NumberNode$v1, NumberNode $v2): NumberNode
	{
		return new NumberNode(pow($v1->getValue(), 1 / (float)$v2->getValue()));
    }
	
	public static function sin(NumberNode$v1): NumberNode
	{
		return new NumberNode(sin($v1->getValue()));
    }
	
	public static function sinh(NumberNode$v1): NumberNode
	{
		return new NumberNode(sinh($v1->getValue()));
    }
	
	public static function sqrt(NumberNode$v1): NumberNode
	{
		return new NumberNode(sqrt($v1->getValue()));
    }
	
	public static function tan(NumberNode$v1): NumberNode
	{
		return new NumberNode(tan($v1->getValue()));
    }
	
	public static function tanh(NumberNode$v1): NumberNode
	{
		return new NumberNode(tanh($v1->getValue()));
	}
}

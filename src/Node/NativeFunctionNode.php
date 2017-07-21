<?php
/**
 * Mathr\Node\NativeFunctionNode class file.
 * @package Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr\Node;

use SplStack;
use SplFixedArray;
use Mathr\Scope;
use Mathr\Parser\Token;
use Mathr\Exception\IncorrectFunctionParametersException;

class NativeFunctionNode
	extends FunctionDeclNode
{
	const LIST = [
		'abs', 'acos', 'acosh', 'asin', 'asinh', 'atan', 'atanh', 'ceil',
		'cos', 'cosh', 'deg2rad', 'float', 'floor', 'hypot', 'int', 'log',
		'max', 'min', 'mod', 'rad2deg', 'rand', 'round', 'rt',
		'sin', 'sinh', 'sqrt', 'tan', 'tanh'
	];
	
	/**
	 * Informs the number of parameters needed for each function, when it is
	 * not the assumed value of 1. If a negative value is given, then this
	 * function can receive more parameters but no less than the absolute value
	 * informed. Positive values inform the exact number of required parameters.
	 * @var int[] Needed parameters for functions.
	 */
	protected static $fargc = [
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
	 * NativeFunctionNode constructor.
	 * @param string $value Function name.
	 * @param SplStack $stack Operand stack.
	 * @throws IncorrectFunctionParametersException
	 */
	public function __construct(string $value, SplStack $stack)
	{
		parent::__construct($value, $stack);
		$allowed = self::$fargc[$this->value] ?? 1;
		
		if($this->argc < abs($allowed) || ($allowed >= 0 && $this->argc > $allowed))
			throw new IncorrectFunctionParametersException($this->value);
	}
	
	/**
	 * @inheritdoc
	 */
	public function evaluate(Scope $scope) : AbstractNode
	{
		$argv = [];
		
		foreach($this->argv as $arg)
			$argv[] = $arg->evaluate($scope)->value();
		
		return new NumberNode($this->exec($argv));
	}
	
	/**
	 * Executes all native functions with the correct argument types.
	 * @param array $argv Arguments to be sent to function.
	 * @return int|float Evaluated value.
	 */
	protected function exec(array $argv)
	{
		static $func;
		
		if(!isset($func)) {
			srand(time());
			$func = [
				'abs' => function ($v1) {
					return abs($v1);
				},
				'acos' => function ($v1) {
					return acos($v1);
				},
				'acosh' => function ($v1) {
					return acosh($v1);
				},
				'asin' => function ($v1) {
					return asin($v1);
				},
				'asinh' => function ($v1) {
					return asinh($v1);
				},
				'atan' => function ($v1) {
					return atan($v1);
				},
				'atanh' => function ($v1) {
					return atanh($v1);
				},
				'ceil' => function ($v1) {
					return ceil($v1);
				},
				'cos' => function ($v1) {
					return cos($v1);
				},
				'cosh' => function ($v1) {
					return cosh($v1);
				},
				'deg2rad' => function ($v1) {
					return deg2rad($v1);
				},
				'float' => function ($v1) {
					return (float)$v1;
				},
				'floor' => function ($v1) {
					return floor($v1);
				},
				'hypot' => function ($v1, $v2) {
					return hypot($v1, $v2);
				},
				'int' => function ($v1) {
					return (int)$v1;
				},
				'log' => function ($v1, $exp = M_E) {
					return log($v1, $exp);
				},
				'max' => function (...$v) {
					return max(...$v);
				},
				'min' => function (...$v) {
					return min(...$v);
				},
				'mod' => function ($v1, $v2) {
					if(is_float($v1) || is_float($v2))
						return fmod($v1, $v2);
					return $v1 % $v2;
				},
				'rad2deg' => function ($v1) {
					return rad2deg($v1);
				},
				'rand' => function () {
					return rand();
				},
				'round' => function($v1, $v2 = 0) {
					return round($v1, $v2);
				},
				'rt' => function($v1, $v2) {
					return pow($v1, 1 / (float)$v2);
				},
				'sin' => function($v1) {
					return sin($v1);
				},
				'sinh' => function($v1) {
					return sinh($v1);
				},
				'sqrt' => function($v1) {
					return sqrt($v1);
				},
				'tan' => function($v1) {
					return tan($v1);
				},
				'tanh' => function($v1) {
					return tanh($v1);
				}
			];
		}
		
		return $func[$this->value](...$argv);
	}
	
}

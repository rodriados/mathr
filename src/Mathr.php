<?php
/**
 * Mathr\Mathr class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017-2018 Rodrigo Siqueira
 */
namespace Mathr;

use Mathr\Node\FunctionNode;
use Mathr\Node\VariableNode;
use Mathr\Exception\MathrException;

class Mathr
{
	/**
	 * Parser instance.
	 * @var Parser Instance responsible for parsing the expressions.
	 */
	protected $parser;
	
	/**
	 * Scope instance.
	 * @var Scope Instance responsible for storing all variables and functions.
	 */
	protected $scope;
	
	/**
	 * Mathr constructor.
	 * This constructor simply initializes the scope and parser instances
	 * needed for evaluating mathematical expressions.
	 */
	public function __construct()
	{
		srand(time());
		$this->parser = new Parser;
		$this->scope = new Scope;
	}
	
	/**
	 * Evaluates a mathematical expression, stores its definition or returns a
	 * node as an expected value.
	 * @param string $expr Expression to be evaluated.
	 * @return number|array Value of all numeric expressions.
	 */
	public function evaluate(string $expr)
	{
		$expr = explode(';', trim($expr, ';'));
		$return = [];
		$ret = [];
		
		foreach($expr as $command)
			$return[] = $this->parser
				->parse($command)
				->evaluate($this->scope);
		
		foreach($return as &$r)
			if($r instanceof Node\NumberNode)
				$ret[] = $r->getValue();
		
		return count($ret) == 1 ? $ret[0] : $ret;
	}
	
	/**
	 * Stores a variable into the scope.
	 * @param string $name The variable name.
	 * @param string $value The variable value.
	 */
	public function setVariable(string $name, string $value)
	{
		$this->scope->setVariable(
			new VariableNode('$'.trim($name)),
			$this->parser->parse($value)->evaluate($this->scope)
		);
	}
	
	/**
	 * Stores a function into the scope.
	 * @param string $decl The function declaration.
	 * @param string $block The function block.
	 * @throws MathrException
	 */
	public function setFunction(string $decl, string $block)
	{
		$decl = $this->parser->parse($decl)->build();
		$block = $this->parser->parse($block)->build();
		
		if(!$decl instanceof FunctionNode)
			throw new MathrException;
		
		$this->scope->setFunction(
			$decl->processBody($block),
			$block
		);
	}
}

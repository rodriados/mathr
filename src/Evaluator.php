<?php
/**
 * Mathr\Evaluator class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr;

class Evaluator
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
		$this->scope = new Scope;
		$this->parser = new Parser;
	}
	
	/**
	 * Evaluates a mathematical expression, stores its definition or returns a
	 * node as an expected value.
	 * @param string $expr Expression to be evaluated.
	 * @return array Value of all numeric expressions.
	 */
	public function evaluate(string $expr): array
	{
		$expr = explode(';', trim($expr, ';'));
		$return = [];
		$ret = [];
		
		foreach($expr as $command)
			$return[] = $this->parser
				->parse(trim($command))
				->evaluate($this->scope);
		
		foreach($return as &$r)
			if($r instanceof Node\NumberNode)
				$ret[] = $r->value();
		
		return $ret;
	}
	
}

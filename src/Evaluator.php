<?php
/**
 * Mathr\Parser class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr;

use Mathr\Node\NullNode;
use Mathr\Node\NumberNode;

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
	 * Evaluator constructor.
	 */
	public function __construct()
	{
		$this->parser = new Parser;
		$this->scope = new Scope;
	}
	
	/**
	 * Evaluates a mathematical expression, stores its definition or returns a
	 * node as an expected value.
	 * @param string $expr Expression to be evaluated.
	 * @return Node\AbstractNode Evaluated expression value.
	 */
	public function evaluate(string $expr)
	{
		$expr = explode(';', trim($expr, ';'));
		$return = [];
		
		foreach($expr as $command)
			$return[] = $this->parser
				->parse(trim($command))
				->evaluate($this->scope);
		
		foreach($return as &$ret)
			if(!$ret instanceof NumberNode)
				$ret = new NullNode;
		
		return $return[0];
	}
	
}

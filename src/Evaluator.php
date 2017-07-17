<?php
/**
 * Mathr\Parser class file.
 * @package Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr;

use Mathr\Node\NullNode;
use Mathr\Node\NumberNode;

class Evaluator
{
	protected $parser;
	public $scope;
	
	public function __construct()
	{
		$this->parser = new Parser;
		$this->scope = new Scope;
	}
	
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
